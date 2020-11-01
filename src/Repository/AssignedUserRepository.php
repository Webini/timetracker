<?php

namespace App\Repository;

use App\Entity\AssignedUser;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssignedUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssignedUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssignedUser[]    findAll()
 * @method AssignedUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignedUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssignedUser::class);
    }

    /**
     * @param Project|int|string $project
     * @param string|int|User $user
     * @return AssignedUser|null
     *@throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findForProjectAndUser($project, $user): ?AssignedUser
    {
        try {
            return $this
                ->createQueryBuilder('a')
                ->where('a.project = :project')
                ->setParameter('project', $project)
                ->andWhere('a.assigned = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleResult()
            ;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param Project $project
     * @return int|mixed|string
     */
    public function findAllForProject(Project $project)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->where('a.project = :project')
            ->setParameter('project', $project)
        ;

        $query = $qb->getQuery();
        $query->setFetchMode(User::class, 'a.assigned', ClassMetadataInfo::FETCH_EAGER);

        return $query->getResult();
    }
}
