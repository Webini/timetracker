<?php


namespace App\Normalizer;


use App\Entity\TaskTimer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class TaskTimerNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException(sprintf('Cannot normalize object "%s" because the injected serializer is not a normalizer', $object));
        }

        $context[self::class] = true;

        if (empty($context['groups'])) {
            $context['groups'] = [ 'task_timer_full' ];
        }

        $data = $this->serializer->normalize($object, $format, $context);

        if (isset($context['withTask']) && $context['withTask']) {
            $data['task'] = $this->serializer->normalize(
                $object->getTask(),
                $format,
                array_merge($context, [ 'groups' => [ 'task_short' ]]),
            );
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (isset($context[self::class])) {
            return false;
        }

        return $data instanceof TaskTimer;
    }
}
