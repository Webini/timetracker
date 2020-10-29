<?php

namespace App\Model;

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
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     * @return UserSearch
     */
    public function setSearch(?string $search): UserSearch
    {
        $this->search = $search;
        return $this;
    }
}