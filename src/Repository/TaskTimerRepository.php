<?php

namespace App\Repository;

use App\Entity\TaskTimer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
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
