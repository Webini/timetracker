<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TaskTimer;
use App\Entity\User;
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
     * @param Task[] $tasks
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @param User|string|integer|null $for
     */
    public function processTimeSpent(array $tasks, $for = null, ?\DateTime $from = null, ?\DateTime $to = null)
    {
        if ($from && !$to || $to && !$from) {
            throw new \Exception('You must set DateTime $from and DateTime $to parameters');
        }

        $qb = $this
            ->createQueryBuilder('t')
            ->select('IDENTITY(t.task) as taskId')
            ->addSelect('IDENTITY(t.owner) as owner')
//            ->addSelect('CURRENT_TIMESTAMP() as ct')
//            ->addSelect('t.id')
            ->addSelect('(CASE WHEN MIN(t.stoppedAt) IS NULL THEN true ELSE false END) as hasRunningTimer')
            ->where('t.task IN (:tasks)')
            ->setParameter('tasks', $tasks)
            ->groupBy('t.task, t.owner')
        ;


        /**
         * it will extract duration for each entries
         * if stoppedAt is null, it mean that the timer is running and we use NOW() as a end date fallback
         * if we are on a timer that overlap the $from and $to, we use LEAST and GREATEST to cut the date at
         * the given $from and $to
         **/
        if ($from && $to) {
            $qb
                ->addSelect('EXTRACT(epoch FROM SUM(LEAST(:to, COALESCE(t.stoppedAt, CURRENT_TIMESTAMP())) - GREATEST(:from, t.startedAt))) as duration')
                ->andWhere(':from <= COALESCE(t.stoppedAt, CURRENT_TIMESTAMP()) and :to >= t.startedAt')
                ->setParameter('from', $from, Types::DATETIMETZ_MUTABLE)
                ->setParameter('to', $to, Types::DATETIMETZ_MUTABLE)
            ;
        } else {
            $qb->addSelect('EXTRACT(epoch FROM SUM(COALESCE(t.stoppedAt, CURRENT_TIMESTAMP()) - t.startedAt)) as duration');
        }


        if ($for !== null) {
            $qb
                ->andWhere('t.owner = :user')
                ->setParameter('user', $for)
            ;
        }
        var_dump($qb->getQuery()->getSQL());
        $results = $qb->getQuery()->getResult();
        die(var_dump($results));

        return $results;
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
