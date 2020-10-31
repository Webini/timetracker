<?php


namespace App\Tests\Behat\Traits;


use App\Entity\Project;
use App\Entity\User;
use App\Manager\ProjectManager;

trait ProjectContextTrait
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @required
     * @param ProjectManager $projectManager
     * @return $this
     */
    public function setProjectManager(ProjectManager $projectManager): self
    {
        $this->projectManager = $projectManager;
        return $this;
    }

    /**
     * @param string $name
     * @param User|null $createdBy
     * @return Project
     */
    private function createProject(string $name, ?User $createdBy = null): Project
    {
        $project = $this->projectManager->create(null, $createdBy);
        $project->setName($name);

        $em = $this->getEntityManager();
        $em->persist($project);
        $em->flush();

        return $project;
    }

    /**
     * @Given /^i create a project named "(.+)" saved in (\S+)$/
     * @param string $name
     * @param string $path
     */
    public function iAddProject(string $name, string $path): void
    {
        $me = $this->getMe();
        $project = $this->createProject($name, $me);
        $this->accessor->setValue($this->bucket, $path, $project);
    }

    /**
     * @Given /^a project named "(.+)" created by (\S+) saved in (\S+)$/
     * @param string $name
     * @param string $userPath
     * @param string $path
     */
    public function addProject(string $name, string $userPath, string $path): void
    {
        $creator = $this->strictAccessor->getValue($this->bucket, $userPath);
        $project = $this->createProject($name, $creator);
        $this->accessor->setValue($this->bucket, $path, $project);
    }
}