<?php

namespace App\Repository;

use App\Entity\TaskProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskProvider[]    findAll()
 * @method TaskProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskProvider::class);
    }

    // /**
    //  * @return TaskProvider[] Returns an array of TaskProvider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskProvider
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
