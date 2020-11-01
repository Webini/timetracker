<?php


namespace App\Tests\Behat\Traits;


use App\Entity\Project;
use App\Entity\Task;
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
     * @return Task
     */
    private function createTask(Project $project): Task
    {
        $task = $this->taskManager->createFor($project);
        $faker = Factory::create();
        $task
            ->setName($faker->sentence())
            ->setDescription($faker->paragraph())
        ;
        $this->em->persist($task);
        $this->em->flush($task);
        return $task;
    }

    /**
     * @Given /^a new task created for project (\S+)$/
     * @param string $projectPath
     */
    public function iCreateTask(string $projectPath): void
    {
        $project = $this->strictAccessor->getValue($this->bucket, $projectPath);
        $this->createTask($project);
    }

    /**
     * @Given /^a new task created for project (\S+) saved in (\S+)$/
     * @param string $projectPath
     * @param string $path
     */
    public function iCreateTaskSavedIn(string $projectPath, string $path): void
    {
        $project = $this->strictAccessor->getValue($this->bucket, $projectPath);
        $task = $this->createTask($project);
        $this->accessor->setValue($this->bucket, $path, $task);
    }
}