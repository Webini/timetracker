<?php


namespace App\Tests\Behat\Traits;


use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Manager\TaskManager;
use Faker\Factory;

trait TaskContextTrait
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @required
     * @param TaskManager $taskManager
     * @return $this
     */
    public function setTaskManager(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
        return $this;
    }

    /**
     * @param Project $project
     * @param User|null $createdBy
     * @return Task
     */
    private function createTask(Project $project, ?User $createdBy = null): Task
    {
        $task = $this->taskManager->createFor($project);
        $faker = Factory::create();
        $task
            ->setName($faker->sentence())
            ->setDescription($faker->paragraph())
            ->setCreatedBy($createdBy)
        ;
        $this->em->persist($task);
        $this->em->flush($task);
        return $task;
    }

    /**
     * @Given /^a new task created for project (\S+)$/
     * @param string $projectPath
     */
    public function aNewCreatedTask(string $projectPath): void
    {
        $project = $this->strictAccessor->getValue($this->bucket, $projectPath);
        $this->createTask($project);
    }

    /**
     * @Given /^a new task created for project (\S+) saved in (\S+)$/
     * @param string $projectPath
     * @param string $path
     */
    public function aNewCreatedTaskSavedIn(string $projectPath, string $path): void
    {
        $project = $this->strictAccessor->getValue($this->bucket, $projectPath);
        $task = $this->createTask($project);
        $this->accessor->setValue($this->bucket, $path, $task);
    }

    /**
     * @Given /^i create a new task for project (\S+) saved in (\S+)$/
     * @param string $projectPath
     * @param string $path
     */
    public function iCreateANewTaskSavedIn(string $projectPath, string $path): void
    {
        $me = $this->getMe();
        if ($me === null) {
            throw new \RuntimeException('Cannot found current user');
        }

        $project = $this->strictAccessor->getValue($this->bucket, $projectPath);
        $task = $this->createTask($project, $me);
        $this->accessor->setValue($this->bucket, $path, $task);
    }
}