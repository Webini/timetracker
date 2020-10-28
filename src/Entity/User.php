<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\TaskProvider;
use App\Entity\Project;

/**
 * @ORM\Entity() #repositoryClass="App\Repository\UserRepository"
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class User implements \Serializable, UserInterface
{
    use TimestampableEntity;

    const ROLE_USER = 1;
    const ROLE_PROJECT_MANAGER = 2;
    const ROLE_ADMIN = 128;
    const ROLE_SUPER_ADMIN = 256;

    const BUNDLE_USER = self::ROLE_USER;
    const BUNDLE_PROJECT_MANAGER = self::ROLE_USER | self::ROLE_PROJECT_MANAGER;
    const BUNDLE_ADMIN = self::BUNDLE_PROJECT_MANAGER | self::ROLE_ADMIN;
    const BUNDLE_SUPER_ADMIN = self::ROLE_ADMIN | self::ROLE_SUPER_ADMIN;

    const ROLES = [
        self::ROLE_USER => 'ROLE_USER',
        self::ROLE_PROJECT_MANAGER => 'ROLE_PROJECT_MANAGER',
        self::ROLE_ADMIN => 'ROLE_ADMIN',
        self::ROLE_SUPER_ADMIN => 'ROLE_SUPER_ADMIN',
    ];

    /**
     * @var integer|null
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({ "user_full", "user_short" })
     */
    private $id;

    /**
     * @var string|null
     * @Assert\Email()
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @Groups({ "user_full" })
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var bool|null
     * @ORM\Column(name="email_validated", type="boolean", options={"default"=false})
     * @Groups({ "user_full" })
     */
    private $emailValidated;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Groups({ "user_full", "user_short" })
     */
    private $firstName;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Groups({ "user_full", "user_short" })
     */
    private $lastName;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     * @Groups({ "user_full" })
     */
    private $phoneNumber;

    /**
     * @var integer
     * @ORM\Column(type="integer", name="roles", options={"unsigned": true, "default": 1})
     * @Groups({ "user_full", "user_short" })
     */
    private $roles;

    /**
     * @Assert\Length(groups={"password"}, min=6)
     * @var string|null
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity=TaskProvider::class, mappedBy="owner", orphanRemoval=true)
     */
    private $taskProviders;

    /**
     * @ORM\OneToMany(targetEntity=AssignedProject::class, mappedBy="assigned", orphanRemoval=true)
     */
    private $assignedProjects;

    public function __construct()
    {
        $this->roles = self::ROLE_USER;
        $this->emailValidated = false;
        $this->taskProviders = new ArrayCollection();
        $this->assignedProjects = new ArrayCollection();
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
        ));
    }

    /**
     * @param $serialized
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
        ) = unserialize($serialized);
    }

    /**
     * @return bool|null
     */
    public function isEmailValidated(): ?bool
    {
        return $this->emailValidated;
    }

    /**
     * @param boolean|null $validated
     * @return $this
     */
    public function setEmailValidated(?bool $validated): self
    {
        $this->emailValidated = $validated;
        return $this;
    }

    /**
     * @param integer $role cf User::ROLE_*
     * @return $this
     */
    public function removeRole(int $role): self
    {
        if (is_numeric($role)) {
            $this->roles = $this->roles ^ $role;
        } else {
            $this->roles = $this->roles ^ array_reverse(self::ROLES)[$role];
        }
        return $this;
    }

    /**
     * @param integer|string $role cf User::ROLE_*
     * @return $this
     */
    public function addRole($role)
    {
        if (is_numeric($role)) {
            $this->roles |= $role;
        } else {
            $this->roles |= array_reverse(self::ROLES)[$role];
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getOriginalRoles()
    {
        return $this->roles;
    }

    /**
     * @param integer $role cf User::ROLE_*
     * @return bool
     */
    public function hasRole(int $role): bool
    {
        return ($this->roles & $role) > 0;
    }

    /**
     * Récupère les roles sous forme d'un tableau avec leur nom genre [ 'ROLE_USER', 'ROLE_ADMIN' ]
     * @return string[]
     */
    public function getRoles()
    {
        $roles = [];
        foreach (self::ROLES as $role => $name) {
            if ($this->hasRole($role)) {
                $roles[] = $name;
            }
        }

        return $roles;
    }

    /**
     * @param int|\Traversable $roles cf User::ROLE*
     * @return $this
     */
    public function setRoles($roles): self
    {
        if (is_numeric($roles)) {
            $this->roles = $roles;
        } else if ($roles instanceof \Traversable) {
            $this->roles = 0;
            foreach ($roles as $role) {
                $this->addRole($role);
            }
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = mb_strtolower($email);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $plainPassword
     * @return $this
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $password
     * @return $this
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return ucwords(strtolower($this->firstName));
    }

    /**
     * @param string|null $firstName
     * @return $this
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return strtoupper($this->lastName);
    }

    /**
     * @param null|string $lastName
     * @return $this
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName ?? mb_strtoupper($lastName);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * @param bool $admin
     * @return $this
     */
    public function setAdmin(bool $admin): self
    {
        if ($admin) {
            $this->addRole(self::ROLE_ADMIN);
        } else {
            $this->removeRole(self::ROLE_ADMIN);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * @param bool $admin
     * @return $this
     */
    public function setSuperAdmin(bool $admin): self
    {
        if ($admin) {
            $this->addRole(self::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(self::ROLE_SUPER_ADMIN);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->getId();
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection|TaskProvider[]
     */
    public function getTaskProviders(): Collection
    {
        return $this->taskProviders;
    }

    public function addTaskProvider(TaskProvider $taskProvider): self
    {
        if (!$this->taskProviders->contains($taskProvider)) {
            $this->taskProviders[] = $taskProvider;
            $taskProvider->setOwner($this);
        }

        return $this;
    }

    public function removeTaskProvider(TaskProvider $taskProvider): self
    {
        if ($this->taskProviders->contains($taskProvider)) {
            $this->taskProviders->removeElement($taskProvider);
            // set the owning side to null (unless already changed)
            if ($taskProvider->getOwner() === $this) {
                $taskProvider->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AssignedProject[]
     */
    public function getAssignedProjects(): Collection
    {
        return $this->assignedProjects;
    }

    public function addAssignedProject(AssignedProject $assignedProject): self
    {
        if (!$this->assignedProjects->contains($assignedProject)) {
            $this->assignedProjects[] = $assignedProject;
            $assignedProject->setAssigned($this);
        }

        return $this;
    }

    public function removeAssignedProject(AssignedProject $assignedProject): self
    {
        if ($this->assignedProjects->contains($assignedProject)) {
            $this->assignedProjects->removeElement($assignedProject);
            // set the owning side to null (unless already changed)
            if ($assignedProject->getAssigned() === $this) {
                $assignedProject->setAssigned(null);
            }
        }

        return $this;
    }
}
