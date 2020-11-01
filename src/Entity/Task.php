<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use App\Traits\BlameableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\TaskTimer;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({ "task_full" })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({ "task_full" })
     */
    private $externalId;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotBlank()
     * @Groups({ "task_full" })
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({ "task_full" })
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({ "task_full" })
     */
    private $archived = false;

    /**
     * @ORM\OneToMany(targetEntity=TaskTimer::class, mappedBy="task", orphanRemoval=true)
     */
    private $timers;

    public function __construct()
    {
        $this->timers = new ArrayCollection();
        $this->archived = false;
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
     * @return Collection|TaskTimer[]
     */
    public function getTimers(): Collection
    {
        return $this->timers;
    }

    public function addTimer(TaskTimer $timer): self
    {
        if (!$this->timers->contains($timer)) {
            $this->timers[] = $timer;
            $timer->setTask($this);
        }

        return $this;
    }

    public function removeTimer(TaskTimer $timer): self
    {
        if ($this->timers->contains($timer)) {
            $this->timers->removeElement($timer);
            // set the owning side to null (unless already changed)
            if ($timer->getTask() === $this) {
                $timer->setTask(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
