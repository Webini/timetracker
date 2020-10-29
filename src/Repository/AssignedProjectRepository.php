<?php

namespace App\Repository;

use App\Entity\AssignedProject;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssignedProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssignedProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssignedProject[]    findAll()
 * @method AssignedProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignedProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssignedProject::class);
    }

    /**
     * @param Project $project
     * @param int|User $user
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return AssignedProject|null
     */
    public function findForUserAndProject(Project $project, $user): ?AssignedProject
    {
        return $this
            ->createQueryBuilder('a')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->andWhere('a.assignedUsers = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    // /**
    //  * @return AssignedProject[] Returns an array of AssignedProject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssignedProject
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
