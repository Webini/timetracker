<?php

namespace App\Model;

use App\Entity\Project;
use App\Model\Pagination\PaginationInterface;
use App\Model\Pagination\PaginationTrait;

class UserSearch implements PaginationInterface
{
    use PaginationTrait;

    /**
     * @var string|null
     */
    private $search;

    /**
     * @var Project|null
     */
    private $notInProject;

    /**
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     * @return $this
     */
    public function setSearch(?string $search): self
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return Project|null
     */
    public function getNotInProject(): ?Project
    {
        return $this->notInProject;
    }

    /**
     * @param Project|null $notInProject
     * @return $this
     */
    public function setNotInProject(?Project $notInProject): self
    {
        $this->notInProject = $notInProject;
        return $this;
    }
}
