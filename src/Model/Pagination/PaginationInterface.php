<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 03/07/18
 * Time: 10:58
 */

namespace App\Model\Pagination;


interface PaginationInterface
{
    /**
     * @return int
     */
    public function getPage() : int;

    /**
     * @param int|null $page
     * @return PaginationInterface
     */
    public function setPage(?int $page) : self;

    /**
     * @return int|null
     */
    public function getLimit() : ?int;

    /**
     * @param int|null $limit
     * @return PaginationInterface
     */
    public function setLimit(?int $limit) : self;
}