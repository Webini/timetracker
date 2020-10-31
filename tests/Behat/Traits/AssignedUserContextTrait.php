<?php


namespace App\Tests\Behat\Traits;


use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\User;
use App\Manager\AssignedUserManager;
use App\Manager\ProjectManager;

trait AssignedUserContextTrait
{
    /**
     * @var AssignedUserManager
     */
    private $assignedUserManager;

    /**
     * @required
     * @param AssignedUserManager $assignedUserManager
     * @return $this
     */
    public function setAssignedProjectManager(AssignedUserManager $assignedUserManager): self
    {
        $this->assignedUserManager = $assignedUserManager;
        return $this;
    }

    /**
     * @param Project $project
     * @param User $user
     * @param int $permissions
     * @return AssignedUser
     */
    private function createAssignedUser(Project $project, User $user, int $permissions = AssignedUser::PERMISSIONS_TASK_CRUD): AssignedUser
    {
        $assignedUser = $this->assignedUserManager->create(
            $project, $user
        );
        $assignedUser->setPermissions($permissions);

        $em = $this->getEntityManager();
        $em->persist($assignedUser);
        $em->flush();

        return $assignedUser;
    }

    /**
     * Translate string perm to int
     * @param string $permission
     * @return int
     */
    public function getAssignedUserPermission(string $permission)
    {
        $permissions = explode(',', $permission);
        $result = 0;
        foreach ($permissions as $perm) {
            $perm = trim($perm);
            if ($perm === 'create task') {
                $result |= AssignedUser::PERMISSION_CREATE_TASK;
            } else if ($perm === 'read task') {
                $result |= AssignedUser::PERMISSION_READ_TASK;
            } else if ($perm === 'update task') {
                $result |= AssignedUser::PERMISSION_UPDATE_TASK;
            } else if ($perm === 'delete task') {
                $result |= AssignedUser::PERMISSION_DELETE_TASK;
            } else if ($perm === 'admin') {
                $result |= AssignedUser::PERMISSION_PROJECT_ADMIN;
            } else if ($perm === 'crud') {
                $result |= AssignedUser::PERMISSIONS_TASK_CRUD;
            }
        }
        return $result;
    }

    /**
     * @Given /^i assign user (\S+) to project (\S+) with permission ([(create task|read task|update task|delete task|admin|crud)\,]+)$/
     * @param string $userPath
     * @param string $projectPath
     * @param string $permissions
     */
    public function iAssignUserToProject(string $userPath, string $projectPath, string $permissions): void
    {
        $user = $this->strictAccessor->getValue($this->bucket, $userPath);
        $project = $this->strictAccessor->getValue($this->bucket, $projectPath);
        $this->createAssignedUser($project, $user, $this->getAssignedUserPermission($permissions));
    }
}