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

class ProjectVoter extends Voter
{
    use EntityManagerAwareTrait;
    use AuthorizationCheckerAwareTrait;

    const PROJECT_CREATE = 'PROJECT_CREATE';
    const PROJECT_UPDATE = 'PROJECT_UPDATE';
    const PROJECT_READ_FULL = 'PROJECT_READ_FULL';
    const PROJECT_READ = 'PROJECT_READ';

    const ALLOWED_ATTRIBUTES = [
        self::PROJECT_CREATE,
        self::PROJECT_UPDATE,
        self::PROJECT_READ_FULL,
        self::PROJECT_READ,
    ];
    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * ProjectVoter constructor.
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
        if ($attribute === self::PROJECT_CREATE) {
            return true;
        }

        return in_array($attribute, self::ALLOWED_ATTRIBUTES)
            && $subject instanceof Project;
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

        if ($attribute === self::PROJECT_CREATE) {
            return $this->canCreate();
        }
        if ($attribute === self::PROJECT_UPDATE) {
            return $this->canUpdate($user, $subject);
        }
        if ($attribute === self::PROJECT_READ_FULL) {
            return $this->canReadFull($user, $subject);
        }
        if ($attribute === self::PROJECT_READ) {
            return $this->canRead($user, $subject);
        }

        return false;
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function canRead(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);
        return $assignedUser !== null;
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function canReadFull(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);
        if ($assignedUser !== null) {
            return $assignedUser->hasPermissions(AssignedUser::PERMISSION_PROJECT_ADMIN);
        }

        return false;
    }

    /**
     * Admin and super admin can update a project
     * Project managers can update the project only if they are project admin
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function canUpdate(User $user, Project $project): bool
    {
        if ($this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            return true;
        }

        $assignedUser = $this->assignedUserManager->getAssignedUserFor($project, $user);
        if ($assignedUser !== null) {
            return $assignedUser->hasPermissions(AssignedUser::PERMISSION_PROJECT_ADMIN);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        return $this->authorizationChecker->isGranted(User::ROLES[User::ROLE_PROJECT_MANAGER]);
    }
}
