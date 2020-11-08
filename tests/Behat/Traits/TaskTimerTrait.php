<?php


namespace App\Tests\Behat\Traits;


use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Entity\User;
use App\Manager\TaskTimerManager;
use App\Manager\TimeZoneManager;
use App\Model\TimerModel;
use Faker\Factory;

trait TaskTimerTrait
{
    /**
     * @var TaskTimerManager
     */
    private $taskTimerManager;

    /**
     * @var TimeZoneManager
     */
    private $timeZoneManager;

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
     * @required
     * @param TimeZoneManager $timeZoneManager
     * @return $this
     */
    public function setTimeZoneManager(TimeZoneManager $timeZoneManager): self
    {
        $this->timeZoneManager = $timeZoneManager;
        return $this;
    }

    /**
     * @param User $owner
     * @param Task $task
     * @return TaskTimer
     * @throws \Exception
     */
    private function createRunningTimer(User $owner, Task $task): TaskTimer
    {
        $faker = Factory::create();
        $timer = new TimerModel();
        $timer
            ->setTask($task)
            ->setNote($faker->paragraph)
        ;

        $taskTimer = $this->taskTimerManager->createFor($owner, $timer);
        $this->em->persist($taskTimer);
        $this->em->flush();

        return $taskTimer;
    }

    /**
     * @param User $owner
     * @param Task $task
     * @param \DateTime $startedAt
     * @param string $hours
     * @param string $minutes
     * @return TaskTimer
     * @throws \Exception
     */
    private function createTimer(User $owner, Task $task, \DateTime $startedAt, string $hours, string $minutes): TaskTimer
    {
        $timer = new TimerModel();
        $timer
            ->setTask($task)
            ->setStartedAt($startedAt)
            ->setHours((int)$hours)
            ->setMinutes((int)$minutes)
        ;

        $taskTimer = $this->taskTimerManager->createFor($owner, $timer);
        $this->em->persist($taskTimer);
        $this->em->flush();

        return $taskTimer;
    }


    /**
     * @Given /^a running timer for task (.+) and user (.+)$/
     * @param string $taskPath
     * @param string $userPath
     */
    public function aRunningTimerForTaskAndUser(string $taskPath, string $userPath): void
    {
        $this->aRunningTimerForTaskAndUserSavedIn($taskPath, $userPath);
    }

    /**
     * @Given /^a running timer for task (.+) and user (.+) saved in (.+)$/
     */
    public function aRunningTimerForTaskAndUserSavedIn(string $taskPath, string $userPath, ?string $path = null): void
    {
        $task = $this->strictAccessor->getValue($this->bucket, $taskPath);
        $user = $this->strictAccessor->getValue($this->bucket, $userPath);
        $timer = $this->createRunningTimer($user, $task);

        if ($path !== null) {
            $this->accessor->setValue($this->bucket, $path, $timer);
        }
    }

    /**
     * @Given /^i have a running timer for task (.+)$/
     * @param string $taskPath
     * @throws \Exception
     */
    public function iHaveARunningTimer(string $taskPath): void
    {
        $this->iHaveARunningTimerSavedIn($taskPath);
    }

    /**
     * @Given /^i have a running timer for task (.+) saved in (.+)$/
     * @param string $taskPath
     * @param string|null $path
     * @throws \Exception
     */
    public function iHaveARunningTimerSavedIn(string $taskPath, ?string $path = null): void
    {
        $me = $this->getMe();
        if ($me === null) {
            throw new \RuntimeException('Cannot found current user');
        }

        $task = $this->strictAccessor->getValue($this->bucket, $taskPath);
        $timer = $this->createRunningTimer($me, $task);
        if ($path !== null) {
            $this->accessor->setValue($this->bucket, $path, $timer);
        }
    }

    /**
     * @Given /^a timer started at ([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}) during ([0-9]+)h ([0-9]+)m for task (.+) and user (.+)( created by (.+) saved in (.+))?$/
     * @param string $strStartedAt
     * @param string $hours
     * @param string $minutes
     * @param string $taskPath
     * @param string $userPath
     * @param string|null $createdByPath
     * @param string|null $savedPath
     * @throws \Exception
     */
    public function aStoppedTimer(string $strStartedAt, string $hours, string $minutes, string $taskPath, string $userPath, ?string $createdByPath = null, ?string $savedPath = null): void
    {
        $task = $this->strictAccessor->getValue($this->bucket, $taskPath);
        $owner = $this->strictAccessor->getValue($this->bucket, $userPath);
        $createdBy = $this->getMe();
        if ($createdByPath !== null) {
            $createdBy = $this->strictAccessor->getValue($this->bucket, $createdByPath);
        }

        $startedAt = $this->timeZoneManager->createDate($strStartedAt, $createdBy);
        $timer = $this->createTimer($owner, $task, $startedAt, $hours, $minutes);
        if ($savedPath !== null) {
            $this->accessor->setValue($this->bucket, $savedPath, $timer);
        }
    }

}
