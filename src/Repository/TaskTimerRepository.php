<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Entity\User;
use App\DTO\TaskTimerAggregate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTimer|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTimer|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTimer[]    findAll()
 * @method TaskTimer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTimerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTimer::class);
    }

    /**
     * @param User $user
     * @return TaskTimer|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findRunningTimerFor(User $user): ?TaskTimer
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.owner = :user')
            ->setParameter('user', $user)
            ->andWhere('t.startedAt IS NOT NULL')
            ->andWhere('t.stoppedAt IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param Task|string|integer|array|null $tasks
     * @param User|string|integer|null $for
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return \Doctrine\ORM\QueryBuilder
     * @throws \Exception
     */
    public function createTimeSpentQuery($tasks = null, $for = null, ?\DateTime $from = null, ?\DateTime $to = null): \Doctrine\ORM\QueryBuilder
    {
        if ($from && !$to || $to && !$from) {
            throw new \Exception('You must set DateTime $from and DateTime $to parameters');
        }

        $qb = $this
            ->createQueryBuilder('rtt')
//            ->getEntityManager()
//            ->createQueryBuilder()
//            ->from(TaskTimer::class, 'rtt')
            ->select('IDENTITY(rtt.task) as task')
            ->addSelect('IDENTITY(rtt.owner) as owner')
            ->addSelect('(' .
                $this
                    ->createQueryBuilder('srtt')
                    ->select('srtt.startedAt')
                    ->where('srtt.task = rtt.task')
                    ->andWhere('srtt.owner = rtt.owner')
                    ->andWhere('srtt.stoppedAt IS NULL')
                    ->andWhere('srtt.startedAt IS NOT NULL')
                    ->setMaxResults(1)
                    ->getDQL()
                . ') as runningTimerStartedAt'
            )
//            ->leftJoin('rtt.owner', 'owner')
//            ->select('IDENTITY(rtt.task)')
//            ->addSelect('(CASE WHEN MIN(rtt.stoppedAt) IS NULL THEN true ELSE false END) as hasRunningTimer')
//            ->addSelect('(CASE WHEN MIN(rtt.stoppedAt) IS NULL THEN rtt.started_at ELSE NULL END) as runningStartedAt')
            ->groupBy('rtt.task, rtt.owner')
        ;

//        /**
//         * it will extract duration for each entries
//         * if stoppedAt is null, it mean that the timer is running and we use NOW() as an end date fallback
//         * if we are on a timer that overlap the $from and $to, we use LEAST and GREATEST to cut the date at
//         * the given $from and $to
//         **/
//        if ($from && $to) {
//            $qb
//                ->addSelect('EXTRACT(epoch FROM SUM(LEAST(:to, COALESCE(rtt.stoppedAt, CURRENT_TIMESTAMP())) - GREATEST(:from, rtt.startedAt))) as duration')
//                ->andWhere(':from <= COALESCE(rtt.stoppedAt, CURRENT_TIMESTAMP()) and :to >= rtt.startedAt')
//                ->setParameter('from', $from, Types::DATETIMETZ_MUTABLE)
//                ->setParameter('to', $to, Types::DATETIMETZ_MUTABLE)
//            ;
//        } else {
//            $qb->addSelect('EXTRACT(epoch FROM SUM(COALESCE(rtt.stoppedAt, CURRENT_TIMESTAMP()) - rtt.startedAt)) as duration');
//        }

        $qb
            ->addSelect('EXTRACT(epoch FROM SUM(COALESCE(rtt.stoppedAt, rtt.startedAt) - rtt.startedAt)) as duration')
//            ->andWhere('rtt.startedAt IS NOT NULL')
//            ->andWhere('rtt.stoppedAt IS NOT NULL')
        ;

        if ($from && $to) {
            $qb
                ->andWhere(':from <= COALESCE(rtt.stoppedAt, CURRENT_TIMESTAMP()) and :to >= rtt.startedAt')
                ->setParameter('from', $from, Types::DATETIMETZ_MUTABLE)
                ->setParameter('to', $to, Types::DATETIMETZ_MUTABLE)
            ;
        }

        if ($for !== null) {
            $qb
                ->andWhere('rtt.owner = :user')
                ->setParameter('user', $for)
            ;
        }

        if ($tasks !== null) {
            $qb
                ->andWhere('rtt.task IN (:tasks)')
                ->setParameter('tasks', $tasks)
            ;
        }

        return $qb;
    }

    /**
     * @param Task|string|integer|array|null $tasks
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @param User|string|integer|null $for
     * @return int|mixed|string
     * @throws \Exception
     */
    public function findTimeSpent($tasks = null, $for = null, ?\DateTime $from = null, ?\DateTime $to = null)
    {
        $results = $this
            ->createTimeSpentQuery($tasks, $for, $from, $to)
            ->getQuery()
            ->getResult()
        ;

        $newResults = [];
        foreach ($results as $result) {
            $newResults[] = $this->hydrateTaskTimerAggregateModel($result);
        }

        return $newResults;
    }

    /**
     * @param array|null $result
     * @return \App\DTO\TaskTimerAggregate|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function hydrateTaskTimerAggregateModel(?array $result): ?TaskTimerAggregate
    {
        if ($result === NULL) {
            return NULL;
        }

        $em = $this->getEntityManager();
        $tta = new TaskTimerAggregate();

        $tta
            ->setOwner($em->getReference(User::class, $result['owner']))
            ->setTask($em->getReference(Task::class, $result['task']))
            ->setRunningTimerStartedAt(
                $result['runningTimerStartedAt']
                    ? new \DateTime($result['runningTimerStartedAt'])
                    : NULL
            )
            ->setDuration($result['duration'])
        ;

        return $tta;
    }

    // /**
    //  * @return TaskTimer[] Returns an array of TaskTimer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskTimer
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
