<?php


namespace App\Tests\Behat\Traits;


use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;

trait UserTrait
{
    /**
     * @var EntityManagerInterface|null
     */
    protected $em;

    /**
     * @var UserManager|null
     */
    protected $userManager;

    /**
     * @required
     * @param EntityManagerInterface $em
     * @return $this
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * @required
     * @param UserManager $userManager
     * @return $this
     */
    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
        return $this;
    }

    /**
     * @param string $email
     * @return User|null
     */
    protected function findOneUserByMail(string $email): ?User
    {
        return $this->em
            ->getRepository(User::class)
            ->findOneByEmail($email)
        ;
    }

    /**
     * @return User|null
     */
    public function getAdminUser(): ?User
    {
        return $this->findOneUserByMail(UserFixtures::EMAIL_ADMIN);
    }

    /**
     * @return User|null
     */
    public function getSuperAdminUser(): ?User
    {
        return $this->findOneUserByMail(UserFixtures::EMAIL_SUPER_ADMIN);
    }

    /**
     * @return User|null
     */
    public function getProjectManagerUser(): ?User
    {
        return $this->findOneUserByMail(UserFixtures::EMAIL_PROJECT_MANAGER);
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->findOneUserByMail(UserFixtures::EMAIL_USER);
    }

    /**
     * @param string $type
     * @return User|null
     */
    public function getUserByType(string $type): ?User
    {
        switch ($type) {
            case "user":
                return $this->getUser();
            case "admin":
                return $this->getAdminUser();
            case "super admin":
                return $this->getSuperAdminUser();
            case "project manager":
                return $this->getProjectManagerUser();
        }
        throw new \RuntimeException('Invalid user type ' . $type);
    }

}