<?php


namespace App\Normalizer\Identifier;

use App\Normalizer\Identifier\Annotation\SerializeIdentifier;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as SymfonyObjectNormalizer;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;

class ObjectNormalizer extends SymfonyObjectNormalizer implements NormalizerInterface
{
    const IS_ONE = 1;
    const IS_MANY = 2;

    /**
     * @var array
     */
    private $identifierFieldsCache = [];

    /**
     * @var CachedReader
     */
    private $reader;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ObjectNormalizer constructor.
     * @param CachedReader $reader
     * @param EntityManagerInterface $em
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param PropertyTypeExtractorInterface|null $propertyTypeExtractor
     * @param ClassDiscriminatorResolverInterface|null $classDiscriminatorResolver
     * @param callable|null $objectClassResolver
     * @param array $defaultContext
     */
    public function __construct(
        CachedReader $reader,
        EntityManagerInterface $em,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        callable $objectClassResolver = null,
        array $defaultContext = []
    ) {
        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyAccessor,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $objectClassResolver,
            $defaultContext
        );

        $this->reader = $reader;
        $this->em = $em;
        $this->identifierFieldsCache = [];
    }

    /**
     * @param mixed $object
     * @param array $context
     * @return array
     * @throws \ReflectionException
     */
    protected function getIdentifierFields($object, array $context = [])
    {
        $cacheKey = $this->getCacheKey($object, $context);
        if (isset($this->identifierFieldsCache[$cacheKey])) {
            return $this->identifierFieldsCache[$cacheKey];
        }

        $attributes = $this->getAllowedAttributes($object, $context);
        if ($attributes === false) {
            $this->identifierFieldsCache[$cacheKey] = [];
            return $this->identifierFieldsCache[$cacheKey];
        }

        $fields = [];

        $isHandledByEm = $this->em->contains($object);
        $className = $isHandledByEm
            ? $this->em->getClassMetadata(get_class($object))->name
            : get_class($object)
        ;

        $reflClass = $this->classMetadataFactory
            ->getMetadataFor($className)
            ->getReflectionClass()
        ;

        /** @var AttributeMetadata $attribute */
        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            $property = $reflClass->getProperty($name);

            $annotation = $this->reader->getPropertyAnnotation($property, SerializeIdentifier::class);
            if (empty($annotation)) {
                continue;
            }

            if ($isHandledByEm) {
                $identifierType = $this->getEntityIdentifierType($property);
                $fields[$name] = $identifierType;
            } else {
                $fields[$name] = $this->getObjectIdentifierType($property, $object, $isHandledByEm);
            }
        }

        if (!$isHandledByEm) {
            return $fields;
        }

        $this->identifierFieldsCache[$cacheKey] = $fields;
        return $this->identifierFieldsCache[$cacheKey];
    }

    /**
     * @param \ReflectionProperty $property
     * @return int self::IS_
     */
    private function getEntityIdentifierType(\ReflectionProperty $property): int
    {
        $isMany = (
            !empty($this->reader->getPropertyAnnotation($property, OneToMany::class)) ||
            !empty($this->reader->getPropertyAnnotation($property, ManyToMany::class))
        );

        return $isMany ? self::IS_MANY : self::IS_ONE;
    }

    /**
     * @param \ReflectionProperty $property
     * @param $object
     * @param bool $isHandledByEm
     * @return int
     */
    private function getObjectIdentifierType(\ReflectionProperty $property, $object, bool $isHandledByEm): int
    {
        $data = $this->propertyAccessor->getValue($object, $property->getName());
        if (is_array($data) || $data instanceof \Traversable) {
            return self::IS_MANY;
        }

        return self::IS_ONE;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|mixed|string|null
     * @throws \ReflectionException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException(sprintf('Cannot normalize object "%s" because the injected serializer is not a normalizer', $object));
        }

        $context[self::class] = true;
        $identifiers = $this->getIdentifierFields($object, $context);

        foreach ($identifiers as $name => $type) {
            if (isset($context[self::CALLBACKS][$name])) {
                continue;
            }

            $context[self::CALLBACKS][$name] = [
                $this,
                $type === self::IS_ONE ? 'callbackIdentifier' : 'callbackIdentifiers'
            ];
        }

        if ($this->em->contains($object)) {
            return parent::normalize($object, $format, $context);
        }
        return $this->serializer->normalize($object, $format, $context);
    }

    /**
     * @param mixed $value value of this field
     * @param object $object the whole object being normalized
     * @param string $attributeName name of the attribute being normalized
     * @param string $format the requested format
     * @param array $context the serialization context
     * @param bool $flattenIfSingle flatten results if single id is found
     * @return array|bool|int|float|string|null
     */
    public function callbackIdentifier($value, $object, string $attributeName, string $format, array $context = [], bool $flattenIfSingle = true)
    {
        if ($value === null || is_scalar($value)) {
            return $value;
        }

        $output = [];
        $uof = $this->em->getUnitOfWork();
        $identifiers = $uof->getEntityIdentifier($value);

        foreach ($identifiers as $name => $value) {
            $output[$name] = $value;
        }

        if (count($output) === 1 && $flattenIfSingle) {
            return array_values($output)[0];
        }

        return $output;
    }

    /**
     * @param mixed $values values of this field
     * @param object $object the whole object being normalized
     * @param string $attributeName name of the attribute being normalized
     * @param string $format the requested format
     * @param array $context the serialization context
     * @return array|null
     */
    public function callbackIdentifiers($values, $object, string $attributeName, string $format, array $context = [])
    {
        if ($values === null) {
            return null;
        }

        $output = [];
        foreach ($values as $value) {
            $output[] = $this->callbackIdentifier($value, $object, $attributeName, $format, $context);
        }

        return $output;
    }

    /**
     * @param $object
     * @param array $context
     * @return string
     */
    private function getCacheKey($object, array $context)
    {
        $groups = join('-', $context['groups'] ?? []);
        $ignoredAttributes = join('-', $context[self::IGNORED_ATTRIBUTES] ?? []);
        return md5(get_class($object) . $groups . $ignoredAttributes);
    }


    /**
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::class])) {
            return false;
        }
        return parent::supportsNormalization($data, $format);
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @return bool
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return false;
    }
}
