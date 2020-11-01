<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Traits\AuthorizationCheckerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    use AuthorizationCheckerAwareTrait;

    const USER_CREATE = 'USER_CREATE';
    const USER_CREATE_PROJECT_MANAGER = 'USER_CREATE_PROJECT_MANAGER';
    const USER_CREATE_ADMIN = 'USER_CREATE_ADMIN';
    const USER_CREATE_SUPER_ADMIN = 'USER_CREATE_SUPER_ADMIN';
    const USER_UPDATE = 'USER_UPDATE';
    const USER_READ_FULL = 'USER_READ_FULL';

    const ALL_ATTRIBUTES = [
        self::USER_CREATE,
        self::USER_CREATE_PROJECT_MANAGER,
        self::USER_CREATE_ADMIN,
        self::USER_CREATE_SUPER_ADMIN,
        self::USER_UPDATE,
        self::USER_READ_FULL,
    ];

    const ATTRIBUTES_WITHOUT_SUBJECT = [
        self::USER_CREATE,
        self::USER_CREATE_PROJECT_MANAGER,
        self::USER_CREATE_ADMIN,
        self::USER_CREATE_SUPER_ADMIN,
    ];

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
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
            return $this->canCreate();
        }

        if ($attribute === self::USER_CREATE_ADMIN) {
            return $this->canCreateAdmin();
        }

        if ($attribute === self::USER_CREATE_PROJECT_MANAGER) {
            return $this->canCreateProjectManager();
        }

        if ($attribute === self::USER_CREATE_SUPER_ADMIN) {
            return $this->canCreateSuperAdmin();
        }

        if ($attribute === self::USER_UPDATE) {
            return $this->canUpdate($user, $subject);
        }

        if ($attribute === self::USER_READ_FULL) {
            return $this->canReadFullData($user, $subject);
        }

        return false;
    }

    /**
     * Only Admin, SuperAdmin, PM and ourself can see full infos
     * @param User $user
     * @param User $other
     * @return bool
     */
    public function canReadFullData(User $user, User $other): bool
    {
        if ($user->getId() === $other->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER]);
    }

    /**
     * User can edit an other user only if he is super admin
     * Admin can't edit super admin and other admin
     * Project managers can only edit users
     * @param User $user
     * @param User $other
     * @return bool
     */
    public function canUpdate(User $user, User $other): bool
    {
        if ($user->getId() === $other->getId() ||
            $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_SUPER_ADMIN])) {
            return true;
        }

        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN]) &&
            !$other->hasOneRole(User::ROLE_SUPER_ADMIN | User::ROLE_ADMIN)) {
            return true;
        }

        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER]) &&
            $other->getOriginalRoles() === User::ROLE_USER) {
            return true;
        }

        return false;
    }

    /**
     * Admin, Super Admin, Project manager can create user
     * @return bool
     */
    public function canCreate(): bool
    {
        return $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER]);
    }

    /**
     * Super Admin only can create admin
     * @return bool
     */
    public function canCreateAdmin(): bool
    {
        return $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_SUPER_ADMIN]);

    }

    /**
     * Super Admin only can create super admin
     * @return bool
     */
    public function canCreateSuperAdmin(): bool
    {
        return $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_SUPER_ADMIN]);
    }

    /**
     * Admin, Super Admin can create project manager
     * @return bool
     */
    public function canCreateProjectManager(): bool
    {
        return $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN]);
    }
}
