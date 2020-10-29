<?php


namespace App\Traits;


use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

trait AuthorizationCheckerAwareTrait
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @required
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @return $this
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker): self
    {
        $this->authorizationChecker = $authorizationChecker;
        return $this;
    }

}