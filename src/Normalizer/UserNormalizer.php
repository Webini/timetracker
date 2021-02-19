<?php


namespace App\Normalizer;


use App\Entity\User;
use App\Manager\UserManager;
use App\Security\Voter\UserVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class UserNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserNormalizer constructor.
     * @param UserManager $userManager
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        UserManager $userManager,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->userManager = $userManager;
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

        if (empty($context['groups'])) {
            $token = $this->tokenStorage->getToken();
            if ($token && $this->authorizationChecker->isGranted(UserVoter::USER_READ_FULL, $object)) {
                $context['groups'] = [ 'user_full' ];
            } else {
                $context['groups'] = [ 'user_short' ];
            }
        }

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
        $token = $this->userManager->getJwt($user);
        $refreshToken = $this->userManager->getRefreshToken($user);

        return [
            'token' => $token,
            'refreshToken' => $refreshToken,
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
