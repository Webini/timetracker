<?php

namespace App\Normalizer;

use App\DTO\TaskTimerAggregate;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class TaskTimerAggregateNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $context[self::class] = true;

        if (empty($context['groups'])) {
            $context['groups'] = [ 'task_timer_aggregate_full' ];
        }

        return $this->serializer->normalize($object, $format, $context);
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

        return $data instanceof TaskTimerAggregate;
    }
}
