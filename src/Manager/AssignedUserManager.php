<?php


namespace App\Manager;


use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\User;
use App\Traits\EntityManagerAwareTrait;

class AssignedUserManager
{
    use EntityManagerAwareTrait;

    /**
     * @param Project $project
     * @param User $user
     * @param AssignedUser|null $assignedUser
     * @return AssignedUser
     */
    public function create(Project $project, User $user, ?AssignedUser $assignedUser = null): AssignedUser
    {
        $assignedUser = $assignedUser ?? new AssignedUser();

        $project->addAssignedUser($assignedUser);
        $user->addAssignedProject($assignedUser);

        return $assignedUser;
    }

    /**
     * @param Project $project
     * @param User $user
     * @return AssignedUser|null
     */
    public function getAssignedUserFor(Project $project, User $user): ?AssignedUser
    {
        return $this->em
            ->getRepository(AssignedUser::class)
            ->findForProjectAndUser($project, $user)
        ;
    }

    /**
     * @param AssignedUser $assignedUser
     * @return AssignedUser
     */
    public function delete(AssignedUser $assignedUser): AssignedUser
    {
        return $assignedUser;
    }
}