<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use App\Model\ProjectSearch;
use App\Traits\AuthorizationCheckerAwareTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    use AuthorizationCheckerAwareTrait;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * ProjectRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Project::class);
        $this->paginator = $paginator;
    }

    /**
     * @param int|string|User $user
     * @param ProjectSearch $search
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getMyProjectsQuery($user, ProjectSearch $search)
    {
        $qb = $this->createQueryBuilder('p');

        if (!$this->authorizationChecker->isGranted(User::ROLES[User::ROLE_ADMIN])) {
            $qb
                ->leftJoin('p.assignedUsers', 'au')
                ->andWhere('au.assigned = :me')
                ->setParameter('me', $user)
            ;
        }

        $keywords = $search->getSearch();
        if ($keywords !== null) {
            $qb
                ->andWhere('LOWER(p.name) LIKE :keywords')
                ->setParameter('keywords', '%' . mb_strtolower($keywords) . '%')
            ;
        }

        return $qb;
    }

    /**
     * @param User $user
     * @param ProjectSearch $search
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function searchMyProjectsPaginated(User $user, ProjectSearch $search)
    {
        return $this->paginator->paginate(
            $this->getMyProjectsQuery($user, $search),
            $search->getPage(),
            $search->getLimit()
        );
    }

}
