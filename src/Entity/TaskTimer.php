<?php

namespace App\Entity;

use App\Repository\TaskTimerRepository;
use App\Traits\BlameableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

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
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="timers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $startedAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $stoppedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $owner;

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
}
