<?php

namespace App\Model;

use App\Model\Pagination\PaginationInterface;
use App\Model\Pagination\PaginationTrait;

class ProjectSearch implements PaginationInterface
{
    use PaginationTrait;

    /**
     * @var string|null
     */
    private $search;

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
}