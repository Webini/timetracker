<?php


namespace App\Tests\Behat\Traits;


use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

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

    public function createFakeUser($role = User::BUNDLE_USER): User
    {
        $faker = Factory::create();

        $user = $this
            ->userManager->create()
            ->setEmail($faker->email)
            ->setRoles($role)
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setPhoneNumber($faker->phoneNumber)
            ->setPlainPassword($faker->password(8))
        ;

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param string $type
     * @return User
     */
    public function createFakeUserByType(string $type): User
    {
        switch ($type) {
            case "user":
                return $this->createFakeUser(User::BUNDLE_USER);
            case "admin":
                return $this->createFakeUser(User::BUNDLE_ADMIN);
            case "super admin":
                return $this->createFakeUser(User::BUNDLE_SUPER_ADMIN);
            case "project manager":
                return $this->createFakeUser(User::BUNDLE_PROJECT_MANAGER);
        }
        throw new \RuntimeException('Invalid user type ' . $type);
    }

    /**
     * @param string|int $id
     */
    public function deleteUser($id): void
    {
        $user = $this->em->getRepository(User::class)->findOneById($id);
        if ($user === null) {
            throw new \RuntimeException('Cannot found user ' . $id);
        }

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param string $type
     * @return User
     */
    public function getUserByType(string $type): User
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