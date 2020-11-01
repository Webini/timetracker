<?php

namespace App\Security\Voter;

use App\Entity\AssignedUser;
use App\Entity\Task;
use App\Entity\User;
use App\Manager\AssignedUserManager;
use App\Traits\AuthorizationCheckerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    use AuthorizationCheckerAwareTrait;

    const TASK_CREATE = 'TASK_CREATE';
    const TASK_UPDATE = 'TASK_UPDATE';

    const TASK_ATTRIBUTES = [
        self::TASK_UPDATE
    ];
    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * TaskVoter constructor.
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
        if ($attribute === self::TASK_CREATE) {
            return $subject === null || $subject instanceof AssignedUser;
        }

        return in_array($attribute, self::TASK_ATTRIBUTES)
            && $subject instanceof Task;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::TASK_CREATE) {
            return $this->canCreate($subject);
        }
        if ($attribute === self::TASK_UPDATE) {
            return $this->canUpdate($user, $subject);
        }

        return false;
    }

    /**
     * Admin / SA can always create tasks
     * Project manager / user can create only if they have the permission
     * Other can't create
     * @param AssignedUser|null $assignedUser
     * @return bool
     */
    private function canCreate(?AssignedUser $assignedUser): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        if ($assignedUser === null) {
            return false;
        }

        return $assignedUser->hasPermissions(AssignedUser::PERMISSION_CREATE_TASK);
    }

    /**
     * Admin / super admin can edit any tasks
     * Project manager / users can edit only if they have the permission
     * Other can't edit
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function canUpdate(User $user, Task $task): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($task->getProject(), $user);
        if ($assignedUser === null) {
            return false;
        }

        return $assignedUser->hasPermissions(AssignedUser::PERMISSION_UPDATE_TASK);
    }
}
