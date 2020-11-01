<?php


namespace App\Tests\Behat\Traits;


use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Entity\User;
use App\Manager\TaskTimerManager;
use App\Model\TimerModel;
use Faker\Factory;

trait TaskTimerTrait
{
    /**
     * @var TaskTimerManager
     */
    private $taskTimerManager;

    /**
     * @required
     * @param TaskTimerManager $taskTimerManager
     * @return $this
     */
    public function setTaskTimerManager(TaskTimerManager $taskTimerManager): self
    {
        $this->taskTimerManager = $taskTimerManager;
        return $this;
    }

    /**
     * @param User $owner
     * @param Task $task
     * @param bool $stopped
     * @return TaskTimer
     */
    private function createTimer(User $owner, Task $task, bool $stopped = false): TaskTimer
    {
        $faker = Factory::create();
        $timer = new TimerModel();
        $timer
            ->setTask($task)
            ->setNote($faker->paragraph)
        ;

        if ($stopped) {
            $startedAt = new \DateTime();
            $startedAt->modify('-' . $faker->numberBetween(1, 5) . 'hours');

            $timer
                ->setStartedAt($startedAt)
                ->setHours($faker->numberBetween(0, 12))
                ->setMinutes($faker->numberBetween(0, 60))
            ;
        }

        $taskTimer = $this->taskTimerManager->createFor($owner, $timer);
        $this->em->persist($taskTimer);
        $this->em->flush();

        return $taskTimer;
    }

    /**
     * @Given /^i have a timer running for task (.+)$/
     * @param string $taskPath
     */
    public function iHaveATimerRunning(string $taskPath): void
    {
        $me = $this->getMe();
        if ($me === null) {
            throw new \RuntimeException('Cannot found current user');
        }

        $task = $this->strictAccessor->getValue($this->bucket, $taskPath);
        $this->createTimer($me, $task);
    }

    /**
     * @Given /^i have a timer running for task (.+) saved in (.+)$/
     * @param string $taskPath
     * @param string $path
     */
    public function iHaveATimerRunningSavedIn(string $taskPath, string $path): void
    {
        $me = $this->getMe();
        if ($me === null) {
            throw new \RuntimeException('Cannot found current user');
        }

        $task = $this->strictAccessor->getValue($this->bucket, $taskPath);
        $timer = $this->createTimer($me, $task);
        $this->accessor->setValue($this->bucket, $path, $timer);
    }

}