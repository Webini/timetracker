<?php

namespace App\Repository;

use App\Entity\TaskTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTime[]    findAll()
 * @method TaskTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTime::class);
    }

    // /**
    //  * @return TaskTime[] Returns an array of TaskTime objects
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
    public function findOneBySomeField($value): ?TaskTime
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
