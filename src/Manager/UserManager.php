<?php

namespace App\Manager;

use App\Entity\User;
use App\Normalizer\UserNormalizer;
use Gesdinet\JWTRefreshTokenBundle\EventListener\AttachRefreshTokenOnSuccessListener;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManager;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpFoundation\Response;

class UserManager
{
    /**
     * @var AttachRefreshTokenOnSuccessListener
     */
    private $refreshTokenListener;
    /**
     * @var JWTManager
     */
    private $jwtManager;

    /**
     * @var RefreshTokenManagerInterface
     */
    private $refreshTokenManager;

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
     * @required
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @return $this
     */
    public function setRefreshTokenManager(RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->refreshTokenManager = $refreshTokenManager;
        return $this;
    }

    /**
     * @param User|null $user
     * @return User
     */
    public function create(?User $user = null): User
    {
        return $user ?? new User();
    }

    /**
     * @param User $user
     * @return string
     */
    public function getJwt(User $user): string
    {
        return $this->jwtManager->create($user);
    }

    /**
     * @param User $user
     * @return string
     */
    public function getRefreshToken(User $user): string
    {
        $event = new AuthenticationSuccessEvent([], $user, new Response());
        $this->refreshTokenListener->attachRefreshToken($event);
        return $event->getData()['refreshToken'];
    }
}
