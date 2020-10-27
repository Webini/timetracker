<?php


namespace App\Normalizer;


use App\Entity\User;
use Gesdinet\JWTRefreshTokenBundle\EventListener\AttachRefreshTokenOnSuccessListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class UserNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var AttachRefreshTokenOnSuccessListener
     */
    private $refreshTokenListener;
    /**
     * @var JWTManager
     */
    private $jwtManager;

    /**
     * @param JWTManager $jwtManager
     * @return $this
     */
    public function setJwtManager(JWTManager $jwtManager): self
    {
        $this->jwtManager = $jwtManager;
        return $this;
    }

    /**
     * @param AttachRefreshTokenOnSuccessListener $refreshTokenListener
     * @return $this
     */
    public function setRefreshTokenListener(AttachRefreshTokenOnSuccessListener $refreshTokenListener): self
    {
        $this->refreshTokenListener = $refreshTokenListener;
        return $this;
    }

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
        $data = $this->serializer->normalize($object, $format, $context);

        if (isset($context['jwt']) && $context['jwt']) {
            $data = array_merge($this->retrieveTokens($object), $data);
        }

        return $data;
    }

    /**
     * @param User $user
     * @return array
     */
    private function retrieveTokens(User $user): array
    {
        $token = $this->jwtManager->create($user);

        $event = new AuthenticationSuccessEvent([], $user, new Response());
        $this->refreshTokenListener->attachRefreshToken($event);

        return [
            'token' => $token,
            'refreshToken' => $event->getData()['refreshToken']
        ];
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

        return $data instanceof User;
    }
}