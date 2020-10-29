<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use App\Entity\TaskProvider;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=TaskProvider::class, inversedBy="ownedProjects")
     */
    private $taskProvider;

    /**
     * @ORM\Column(type="guid", unique=true)
     */
    private $guid;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $providerConfiguration = [];

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="project", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity=AssignedProject::class, mappedBy="project", orphanRemoval=true)
     */
    private $assignedUsers;

    public function __construct()
    {
        $this->guid = Uuid::v4();
        $this->tasks = new ArrayCollection();
        $this->assignedUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTaskProvider(): ?TaskProvider
    {
        return $this->taskProvider;
    }

    public function setTaskProvider(?TaskProvider $taskProvider): self
    {
        $this->taskProvider = $taskProvider;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getProviderConfiguration(): ?array
    {
        return $this->providerConfiguration;
    }

    public function setProviderConfiguration(?array $providerConfiguration): self
    {
        $this->providerConfiguration = $providerConfiguration;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AssignedProject[]
     */
    public function getAssignedUsers(): Collection
    {
        return $this->assignedUsers;
    }

    public function addAssignedUser(AssignedProject $assignedProject): self
    {
        if (!$this->assignedUsers->contains($assignedProject)) {
            $this->assignedUsers[] = $assignedProject;
            $assignedProject->setProject($this);
        }

        return $this;
    }

    public function removeAssignedUser(AssignedProject $assignedProject): self
    {
        if ($this->assignedUsers->contains($assignedProject)) {
            $this->assignedUsers->removeElement($assignedProject);
            // set the owning side to null (unless already changed)
            if ($assignedProject->getProject() === $this) {
                $assignedProject->setProject(null);
            }
        }

        return $this;
    }
}
