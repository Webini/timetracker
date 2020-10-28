<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $externalId;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $archived = false;

    /**
     * @ORM\OneToMany(targetEntity=TaskTime::class, mappedBy="task", orphanRemoval=true)
     */
    private $times;

    public function __construct()
    {
        $this->times = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * @return Collection|TaskTime[]
     */
    public function getTimes(): Collection
    {
        return $this->times;
    }

    public function addTime(TaskTime $time): self
    {
        if (!$this->times->contains($time)) {
            $this->times[] = $time;
            $time->setTask($this);
        }

        return $this;
    }

    public function removeTime(TaskTime $time): self
    {
        if ($this->times->contains($time)) {
            $this->times->removeElement($time);
            // set the owning side to null (unless already changed)
            if ($time->getTask() === $this) {
                $time->setTask(null);
            }
        }

        return $this;
    }
}
