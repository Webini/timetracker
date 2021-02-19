<?php

namespace App\Form\Transformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityToIdTransformer.
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    private $em;
    private $entityName;

    public function __construct(EntityManagerInterface $em, string $entityName)
    {
        $this->entityName = $entityName;
        $this->em = $em;
    }

    /**
     * Do nothing.
     *
     * @param object|null $object
     * @return string
     */
    public function transform($object): string
    {
        if (null === $object) {
            return '';
        }

        return current(array_values($this->em->getClassMetadata($this->entityName)->getIdentifierValues($object)));
    }

    /**
     * Transforms an id to an object.
     *
     * @param string|int|null $id
     *
     * @return object|null
     * @throws TransformationFailedException if object is not found
     */
    public function reverseTransform($id): ?object
    {
        if (null === $id) {
            return null;
        }

        $identifier = current(array_values($this->em->getClassMetadata($this->entityName)->getIdentifier()));

        $object = $this->em
            ->getRepository($this->entityName)
            ->findOneBy([$identifier => $id]);

        if (null === $object) {
            throw new TransformationFailedException(sprintf('An object with identifier key "%s" and value "%s" does not exist!', $identifier, $id));
        }

        return $object;
    }
}
