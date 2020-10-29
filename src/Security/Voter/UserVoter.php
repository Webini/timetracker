<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const USER_CREATE = 'USER_CREATE';
    const USER_CREATE_PROJECT_MANAGER = 'USER_CREATE_PROJECT_MANAGER';
    const USER_CREATE_ADMIN = 'USER_CREATE_ADMIN';
    const USER_CREATE_SUPER_ADMIN = 'USER_CREATE_SUPER_ADMIN';

    const ALL_ATTRIBUTES = [
        self::USER_CREATE,
        self::USER_CREATE_PROJECT_MANAGER,
        self::USER_CREATE_ADMIN,
        self::USER_CREATE_SUPER_ADMIN,
    ];

    const ATTRIBUTES_WITHOUT_SUBJECT = [
        self::USER_CREATE,
        self::USER_CREATE_PROJECT_MANAGER,
        self::USER_CREATE_ADMIN,
        self::USER_CREATE_SUPER_ADMIN,
    ];

    /**
     * @param mixed $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (in_array($attribute, self::ATTRIBUTES_WITHOUT_SUBJECT)) {
            return true;
        }

        return in_array($attribute, self::ALL_ATTRIBUTES)
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::USER_CREATE) {
            return $this->canCreate($user);
        }

        if ($attribute === self::USER_CREATE_ADMIN) {
            return $this->canCreateAdmin($user);
        }

        if ($attribute === self::USER_CREATE_PROJECT_MANAGER) {
            return $this->canCreateProjectManager($user);
        }

        if ($attribute === self::USER_CREATE_SUPER_ADMIN) {
            return $this->canCreateSuperAdmin($user);
        }

        return false;
    }

    /**
     * Admin, Super Admin, Project manager can create user
     * @param User|null $user
     * @return bool
     */
    public function canCreate(User $user): bool
    {
        return $user->hasOneRole(User::ROLE_SUPER_ADMIN | User::ROLE_ADMIN | User::ROLE_PROJECT_MANAGER);
    }

    /**
     * Super Admin only can create admin
     * @param User $user
     * @return bool
     */
    public function canCreateAdmin(User $user): bool
    {
        return $user->hasOneRole(User::ROLE_SUPER_ADMIN);
    }

    /**
     * Super Admin only can create super admin
     * @param User $user
     * @return bool
     */
    public function canCreateSuperAdmin(User $user): bool
    {
        return $user->hasOneRole(User::ROLE_SUPER_ADMIN);
    }

    /**
     * Admin, Super Admin can create project manager
     * @param User $user
     * @return bool
     */
    public function canCreateProjectManager(User $user): bool
    {
        return $user->hasOneRole(User::ROLE_ADMIN | User::ROLE_SUPER_ADMIN);
    }
}
