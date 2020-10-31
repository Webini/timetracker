<?php


namespace App\Normalizer;


use App\Entity\Project;
use App\Security\Voter\ProjectVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ProjectNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * ProjectNormalizer constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
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
            if ($token && $this->authorizationChecker->isGranted(ProjectVoter::PROJECT_READ_FULL, $object)) {
                $context['groups'] = [ 'project_full' ];
            } else {
                $context['groups'] = [ 'project_short' ];
            }
        }

        return $this->serializer->normalize($object, $format, $context);
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

        return $data instanceof Project;
    }
}