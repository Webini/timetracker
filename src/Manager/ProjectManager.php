<?php


namespace App\Manager;


use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\User;

class ProjectManager
{
    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * ProjectManager constructor.
     * @param AssignedUserManager $assignedUserManager
     */
    public function __construct(AssignedUserManager $assignedUserManager)
    {
        $this->assignedUserManager = $assignedUserManager;
    }

    /**
     * @param Project|null $project
     * @param User|null $createdBy
     * @return Project
     */
    public function create(?Project $project = null, ?User $createdBy = null): Project
    {
        $project = $project ?? new Project();
        if ($createdBy !== null) {
            $assignedUser = $this->assignedUserManager->create($project, $createdBy);
            $assignedUser->setPermissions(AssignedUser::PERMISSIONS_ALL);
        }
        return $project;
    }


}