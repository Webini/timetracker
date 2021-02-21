<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Model\TaskSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
     * @throws \Exception
     */
    public function createSearchTasksQuery(Project $project, TaskSearch $search): QueryBuilder
    {
        $taskTimerRepo = $this->getEntityManager()->getRepository(TaskTimer::class);

        $qb = $this->
            createQueryBuilder('t')
            ->select('t as entity')
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

        // order by last timers / or task modification
        $query = $taskTimerRepo
            ->createQueryBuilder('tt')
            ->select('GREATEST(t.createdAt, MAX(
                GREATEST(
                    tt.startedAt,
                    COALESCE(tt.stoppedAt, CURRENT_TIMESTAMP())
                )
            ))')
            ->where('tt.task = t')
            ->setMaxResults(1)
        ;
        $qb->addSelect('(' . $query . ') as maxDate');
        $qb->addOrderBy('maxDate', 'DESC');

        return $qb;
    }

    /**
     * @param Project $project
     * @param TaskSearch $search
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     * @throws \Exception
     */
    public function searchPaginated(Project $project, TaskSearch $search)
    {
        $paginated = $this->paginator->paginate(
            $this->createSearchTasksQuery($project, $search),
            $search->getPage(),
            $search->getLimit()
        );

        $items = $paginated->getItems();
        $newItems = [];
        foreach ($items as $key => $item) {
            $newItems[$key] = $item['entity'];
        }

        $paginated->setItems($newItems);

        return $paginated;
    }
}
