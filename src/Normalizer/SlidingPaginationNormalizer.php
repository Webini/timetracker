<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 17/05/18
 * Time: 17:13
 */

namespace App\Normalizer;

use App\Paginator\ExtraFieldsPagination;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class SlidingPaginationNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param SlidingPagination $object Object to normalize
     * @param string|null $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws ExceptionInterface Occurs when the normalizer is not called in an expected context
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException(sprintf('Cannot normalize object "%s" because the injected serializer is not a normalizer', $object));
        }

        $context[self::class] = true;

        $data = $this->serializer->normalize($object, $format, $context);

        return [
            'data' => $data,
            'pagination' => $this->serializer->normalize($object->getPaginationData(), $format, $context),
        ];
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string|null $format The format being (de-)serialized from or into
     *
     * @param array $context
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null, array $context = [])
    {
        if (isset($context[self::class])) {
            return false;
        }

        return $data instanceof SlidingPagination;
    }
}