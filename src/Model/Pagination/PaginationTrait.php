<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 03/07/18
 * Time: 10:59
 */

namespace App\Model\Pagination;

use Symfony\Component\Validator\Constraints as Assert;

trait PaginationTrait
{
    /**
     * @Assert\GreaterThanOrEqual(1)
     * @var int|null
     */
    private $page = 1;

    /**
     * @var int|null
     * @Assert\GreaterThan(0)
     * @Assert\LessThanOrEqual(100)
     */
    protected $limit = 10;

    /**
     * @return int
     */
    public function getPage() : int
    {
        return $this->page ?: 1;
    }

    /**
     * @param int|null $page
     * @return $this
     */
    public function setPage(?int $page) : PaginationInterface
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     * @return $this
     */
    public function setLimit(?int $limit) : PaginationInterface
    {
        $this->limit = $limit;
        return $this;
    }
}
