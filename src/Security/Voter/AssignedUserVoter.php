<?php

namespace App\Security\Voter;

use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\User;
use App\Manager\AssignedUserManager;
use App\Traits\AuthorizationCheckerAwareTrait;
use App\Traits\EntityManagerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AssignedUserVoter extends Voter
{
    use EntityManagerAwareTrait;
    use AuthorizationCheckerAwareTrait;

    const ASSIGNED_USER_CREATE = 'ASSIGNED_USER_CREATE';
    const ASSIGNED_USER_READ = 'ASSIGNED_USER_READ';
    const ASSIGNED_USER_READ_ALL = 'ASSIGNED_USER_READ_ALL';
    const ASSIGNED_USER_DELETE = 'ASSIGNED_USER_DELETE';
    const ASSIGNED_USER_UPDATE = 'ASSIGNED_USER_UPDATE';
    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * AssignedUserVoter constructor.
     * @param AssignedUserManager $assignedUserManager
     */
    public function __construct(AssignedUserManager $assignedUserManager)
    {
        $this->assignedUserManager = $assignedUserManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {
        if ($attribute === self::ASSIGNED_USER_CREATE ||
            $attribute === self::ASSIGNED_USER_DELETE ||
            $attribute === self::ASSIGNED_USER_UPDATE ||
            $attribute === self::ASSIGNED_USER_READ_ALL) {
            return $subject instanceof Project;
        }

        if ($attribute === self::ASSIGNED_USER_READ) {
            return $subject instanceof AssignedUser;
        }

        return false;
//        return in_array($attribute, self::ALLOWED_ATTRIBUTES)
//            && $subject instanceof AssignedUser;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::ASSIGNED_USER_CREATE) {
            return $this->canCreate($user, $subject);
        }
        if ($attribute === self::ASSIGNED_USER_READ_ALL) {
            return $this->canReadAll($user, $subject);
        }
        if ($attribute === self::ASSIGNED_USER_DELETE) {
            return $this->canDelete($user, $subject);
        }
        if ($attribute === self::ASSIGNED_USER_UPDATE) {
            return $this->canUpdate($user, $subject);
        }
        if ($attribute === self::ASSIGNED_USER_READ) {
            return $this->canRead($user, $subject);
        }

        return false;
    }

    /**
     * Admin / SA can edit everyone
     * Project manager can edit users only if they are project's admin
     * Other can't edit
     * @param User $user
     * @param Project $project
     * @return bool
     */
    private function canUpdate(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        // if we are not a PM get out
        if (!$this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER])) {
            return false;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);
        return !!$assignedUser;

    }

    /**
     * Admin / SA can delete anyone
     * PM can delete users only if they are assigned to the project
     * other can't access
     * @param User $user
     * @param Project $project
     * @return bool
     */
    private function canDelete(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        // if we are not a PM get out
        if (!$this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER])) {
            return false;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);
        return !!$assignedUser;
    }

    /**
     * Admin / SA can get all project's users
     * PM can get all users if they are project admin
     * other can't access
     * @param User $user
     * @param Project $project
     * @return bool
     */
    private function canReadAll(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        // if we are not a PM get out
        if (!$this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER])) {
            return false;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);
        return !!$assignedUser;
    }

    /**
     * Admin / SA can retrieve all
     * PM can retrieve all users if they are project admin
     * User can retrieve self
     * other can't do anything
     * @param User $user
     * @param AssignedUser $otherAssigned
     * @return bool
     */
    public function canRead(User $user, AssignedUser $otherAssigned): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        if ($otherAssigned->getAssigned()->getId() === $user->getId()) {
            return true;
        }

        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER])) {
            $assignedUser = $this->assignedUserManager->getAssignedUserFor($otherAssigned->getProject(), $user);
            return !!$assignedUser;
        }

        return false;
    }

    /**
     * Admin / Super admin can assign anyone
     * Project admin can assign only if they are assigned to the project
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function canCreate(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        // if we are not a PM get out
        if (!$this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER])) {
            return false;
        }

        return !!$this->assignedUserManager->getAssignedUserFor($project, $user);
    }
}
