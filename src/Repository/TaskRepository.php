<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use App\Model\TaskSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * TaskRepository constructor.
     * @param ManagerRegistry $registry
     * @param PaginatorInterface $paginator
     */
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        parent::__construct($registry, Task::class);
    }

    /**
     * @param Project|int|string $project
     * @param Task|int|string $task
     * @return Task|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneForProject($project, $task): ?Task
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.project = :project')
            ->setParameter('project', $project)
            ->andWhere('t.id = :task')
            ->setParameter('task', $task)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param Project $project
     * @param TaskSearch $search
     * @return QueryBuilder
     */
    private function searchTasksQuery(Project $project, TaskSearch $search): QueryBuilder
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->where('t.project = :project')
            ->setParameter('project', $project)
        ;

        $keywords = $search->getSearch();
        if ($keywords !== null) {
            $qb
                ->andWhere('LOWER(t.name) LIKE :keywords OR LOWER(t.description) LIKE :keywords')
                ->setParameter('keywords', '%' . mb_strtolower($keywords) . '%')
            ;
        }

        return $qb;
    }

    /**
     * @param Project $project
     * @param TaskSearch $search
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function searchPaginated(Project $project, TaskSearch $search)
    {
        return $this->paginator->paginate(
            $this->searchTasksQuery($project, $search),
            $search->getPage(),
            $search->getLimit()
        );
    }
}
