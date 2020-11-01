<?php

namespace App\Security\Voter;

use App\Entity\AssignedUser;
use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Entity\User;
use App\Manager\AssignedUserManager;
use App\Traits\AuthorizationCheckerAwareTrait;
use Behat\Testwork\Counter\Timer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskTimerVoter extends Voter
{
    use AuthorizationCheckerAwareTrait;

    const TIMER_CREATE = 'TIMER_CREATE';

    const ALL_ATTRIBUTES = [
        self::TIMER_CREATE
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
        return in_array($attribute, self::ALL_ATTRIBUTES)
            && $subject instanceof TaskTimer;
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

        if ($attribute === self::TIMER_CREATE) {
            return $this->canCreate($user, $subject);
        }

        return false;
    }

    /**
     * Admin / SA can create timer for everyone / everything
     * Project manager / user can create timer for projects where they are assigned
     * Project admin can create timer for users assigned to project where he's project admin
     * @param User $user
     * @param Task $task
     * @return bool
     */
    private function canCreate(User $user, TaskTimer $timer): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        $task = $timer->getTask();
        $project = $task->getProject();
        $currentAssignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);

        // we can create timer only for project where we're assigned
        if ($currentAssignedUser === null) {
            return false;
        }

        $owner = $timer->getOwner();
        // we can create timer ourself
        if ($owner->getId() === $user->getId()) {
            return true;
        }

        $timerAssignedUser = $this->assignedUserManager->getAssignedUserFor($project, $owner);
        // we can't create a timer for an user not assigned to the project
        if ($timerAssignedUser === null) {
            return false;
        }

        // we can create timer for other only if we are project admin
        return $currentAssignedUser->hasPermissions(AssignedUser::PERMISSION_PROJECT_ADMIN);
    }
}
