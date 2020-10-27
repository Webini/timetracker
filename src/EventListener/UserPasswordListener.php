<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use app\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordListener
{
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    /**
     * UserPasswordListener constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param User $user
     * @return boolean
     */
    private function encodePassword(User $user) : bool
    {
        if ($user->getPlainPassword() === null || $user->getPlainPassword() === '') { // password est init a ''
            return false;
        }

        $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));

        return true;
    }

    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        return $this->encodePassword($user);
    }

    public function preUpdate(User $user, LifecycleEventArgs $event)
    {
        if ($this->encodePassword($user)) {
            $em = $event->getEntityManager();
            $meta = $em->getClassMetadata(get_class($user));
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        }
    }
}