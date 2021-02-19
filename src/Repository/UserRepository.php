<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use App\Model\UserSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     * @param PaginatorInterface $paginator
     */
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }

    /**
     * @param UserSearch $search
     * @return QueryBuilder
     */
    private function createSearchQuery(UserSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        if ($search->getSearch() !== null) {
            $qb
                ->andWhere('u.email = :search')
                ->setParameter('search', mb_strtolower($search->getSearch()))
                ->orWhere('CONCAT(LOWER(u.firstName), \' \', LOWER(u.lastName)) LIKE :keyword')
                ->setParameter('keyword', '%' . mb_strtolower($search->getSearch()) . '%')
            ;
        }

        if ($search->getNotInProject() !== null) {
            $qb
                ->andWhere(':project NOT MEMBER OF u.assignedProjects')
                ->setParameter('project', $search->getNotInProject())
            ;
        }

        return $qb;
    }

    /**
     * @param UserSearch $search
     * @return PaginationInterface
     */
    public function searchPaginated(UserSearch $search)
    {
        return $this->paginator->paginate(
            $this->createSearchQuery($search),
            $search->getPage(),
            $search->getLimit()
        );
    }
}
