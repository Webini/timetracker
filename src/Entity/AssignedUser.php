<?php

namespace App\Entity;

use App\Normalizer\Identifier\Annotation\SerializeIdentifier;
use App\Repository\AssignedUserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AssignedUserRepository::class)
 * @ORM\Table(
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"assigned_id", "project_id"})
 *   }
 * )
 * @UniqueEntity(
 *   fields={"assigned", "project"},
 *   message="User already added in project"
 * )
 */
class AssignedUser
{
    use TimestampableEntity;

    const PERMISSION_NONE = 0;
    const PERMISSION_CREATE_TASK = 1;
    const PERMISSION_DELETE_TASK = 2;
    const PERMISSION_UPDATE_TASK = 4;

    const PERMISSIONS_TASK_CUD = self::PERMISSION_CREATE_TASK | self::PERMISSION_DELETE_TASK | self::PERMISSION_UPDATE_TASK;
    const PERMISSIONS_ALL = self::PERMISSIONS_TASK_CUD;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({ "assigned_users_full" })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assignedProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $assigned;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="assignedUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="integer")
     * @Groups({ "assigned_users_full" })
     */
    private $permissions;

    public function __construct()
    {
        $this->permissions = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssigned(): ?User
    {
        return $this->assigned;
    }

    public function setAssigned(?User $assigned): self
    {
        $this->assigned = $assigned;

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

    public function getPermissions(): ?int
    {
        return $this->permissions;
    }

    public function setPermissions(int $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function addPermission(int $permission): self
    {
        $this->permissions |= $permission;
        return $this;
    }

    public function removePermission(int $permission): self
    {
        $this->permissions = $this->permissions ^ ($this->permissions & $permission);
        return $this;
    }

    public function hasPermissions(int $permissions): bool
    {
        return ($this->permissions & $permissions) === $permissions;
    }
}
