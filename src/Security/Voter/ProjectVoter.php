<?php

namespace App\Security\Voter;

use App\Entity\AssignedProject;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    const PROJECT_CREATE = 'PROJECT_CREATE';
    const PROJECT_CREATE_TASK = 'PROJECT_CREATE_TASK';

    const ALL_ATTRIBUTES = [
        self::PROJECT_CREATE_TASK,
        self::PROJECT_CREATE_TASK
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProjectVoter constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, self::ALL_ATTRIBUTES)
            && $subject instanceof \App\Entity\Project;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::PROJECT_CREATE) {
            return $this->canCreate($user);
        }
        if ($attribute === self::PROJECT_CREATE_TASK) {
            return $this->canCreateTask($user, $subject);
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canCreate(User $user): bool
    {
        return $user->hasRole(User::ROLE_PROJECT_MANAGER);
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
