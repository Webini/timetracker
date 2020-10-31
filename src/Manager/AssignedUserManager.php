<?php


namespace App\Manager;


use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\User;

class AssignedUserManager
{
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
     * @param AssignedUser $assignedUser
     * @return AssignedUser
     */
    public function delete(AssignedUser $assignedUser): AssignedUser
    {
        return $assignedUser;
    }
}