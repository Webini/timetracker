<?php

namespace App\Security\Voter;

use App\Entity\AssignedProject;
use App\Entity\Project;
use App\Entity\User;
use App\Traits\AuthorizationCheckerAwareTrait;
use App\Traits\EntityManagerAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    use EntityManagerAwareTrait;
    use AuthorizationCheckerAwareTrait;

    const PROJECT_CREATE = 'PROJECT_CREATE';
    const PROJECT_CREATE_TASK = 'PROJECT_CREATE_TASK';

    const ALL_ATTRIBUTES = [
        self::PROJECT_CREATE,
        self::PROJECT_CREATE_TASK
    ];

    const ATTRIBUTES_WITHOUT_SUBJECT = [
        self::PROJECT_CREATE,
    ];

    protected function supports($attribute, $subject)
    {
        if (in_array($attribute, self::ATTRIBUTES_WITHOUT_SUBJECT)) {
            return true;
        }
        return in_array($attribute, self::ALL_ATTRIBUTES)
            && $subject instanceof Project;
    }

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
        if ($attribute === self::PROJECT_CREATE_TASK) {
            return $this->canCreateTask($user, $subject);
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

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function canCreateTask(User $user, Project $project): bool
    {
        $assignedProjectRepo = $this->em->getRepository(AssignedProject::class);
        /** @var AssignedProject|null $assignedProject */
        $assignedProject = $assignedProjectRepo->findForUserAndProject($project, $user);

        if ($assignedProject === null) {
            return false;
        }

        return $assignedProject->hasPermissions(AssignedProject::PERMISSION_ADD_TASK);
    }
}
