<?php

namespace App\Normalizer;

use App\DTO\TaskTimerAggregate;
use App\Entity\Task;
use App\Entity\TaskTimer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class TaskNormalizer implements NormalizerInterface, SerializerAwareInterface
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
        $context[AbstractObjectNormalizer::EXCLUDE_FROM_CACHE_KEY] = [ 'timers' ];

        if (empty($context['groups'])) {
            $context['groups'] = [ 'task_full' ];
        }

        $data = $this->serializer->normalize($object, $format, $context);
        if (isset($context['timers'])) {
            $timers = array_values(array_filter($context['timers'], function(TaskTimerAggregate $timer) use ($object) {
                return $timer->getTask()->getId() === $object->getId();
            }));
            $data['timers'] = $this->serializer->normalize($timers, $format);
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
