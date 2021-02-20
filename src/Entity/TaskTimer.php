<?php

namespace App\Entity;

use App\Normalizer\Identifier\Annotation\SerializeIdentifier;
use App\Repository\TaskTimerRepository;
use App\Traits\BlameableEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskTimerRepository::class)
 */
class TaskTimer
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({ "task_timer_full" })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="timers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @var \DateTime|null
     * @Groups({ "task_timer_full" })
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $startedAt;

    /**
     * @var \DateTime|null
     * @Groups({ "task_timer_full" })
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $stoppedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @var User|null
     * @Groups({ "task_timer_full" })
     * @SerializeIdentifier()
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $owner;

    /**
     * @Groups({ "task_timer_full" })
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $system = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return $this->stoppedAt !== null;
    }

    public function getStoppedAt(): ?\DateTime
    {
        return $this->stoppedAt;
    }

    public function setStoppedAt(?\DateTime $stoppedAt): self
    {
        $this->stoppedAt = $stoppedAt;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getSystem(): ?bool
    {
        return $this->system;
    }

    public function setSystem(bool $system): self
    {
        $this->system = $system;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
