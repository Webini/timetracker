<?php

namespace App\Normalizer;

use App\Entity\Task;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TaskNormalizer implements NormalizerInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = array()): array
    {
        $context[self::class] = true;

        if (empty($context['groups'])) {
            $context['groups'] = [ 'task_full' ];
        }

        $data = $this->normalizer->normalize($object, $format, $context);

        if ($object->getCreatedBy()) {
            $data['createdBy'] = [ 'id' => $object->getCreatedBy()->getId() ];
        }

        return $data;
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

        return $data instanceof Task;
    }
}
