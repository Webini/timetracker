<?php


namespace App\EventListener;


use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthenticationSuccessListener
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * AuthenticationSuccessListener constructor.
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        $userEntity = null;

        if ($user instanceof User) {
            $userEntity = $user;
        } else if ($user instanceof \Symfony\Component\Security\Core\User\User) {
            $userEntity = $this->userProvider->loadUserByUsername($user->getUsername());
        }

        if ($userEntity === null) {
            throw new \Exception('Invalid user');
        }

        $event->setData(array_merge($data, [
            'roles' => $userEntity->getRoles(),
            'id' => $userEntity->getId(),
        ]));
    }

}
