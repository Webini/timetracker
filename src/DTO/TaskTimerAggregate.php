<?php


namespace App\DTO;


use App\Entity\Task;
use App\Normalizer\Identifier\Annotation\SerializeIdentifier;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\User;

class TaskTimerAggregate
{
    /**
     * @Groups({ "task_timer_aggregate_full" })
     * @SerializeIdentifier()
     * @var Task|null
     */
    private $task;

    /**
     * @Groups({ "task_timer_aggregate_full" })
     * @SerializeIdentifier()
     * @var User|null
     */
    private $owner;

    /**
     * @Groups({ "task_timer_aggregate_full" })
     * @var \DateTime|null
     */
    private $runningTimerStartedAt;

    /**
     * @Groups({ "task_timer_aggregate_full" })
     * @var integer
     */
    private $duration;

    /**
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     * @return $this
     */
    public function setTask(?Task $task): self
    {
        $this->task = $task;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return $this
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRunningTimerStartedAt(): ?\DateTime
    {
        return $this->runningTimerStartedAt;
    }

    /**
     * @param \DateTime|null $runningTimerStartedAt
     * @return TaskTimerAggregate
     */
    public function setRunningTimerStartedAt(?\DateTime $runningTimerStartedAt): self
    {
        $this->runningTimerStartedAt = $runningTimerStartedAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return $this
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }
}
