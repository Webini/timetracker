<?php


namespace App\Tests\Behat\Traits;


use App\Entity\User;
use App\Manager\UserManager;
use Faker\Factory;
use \RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

trait UserContextTrait
{
    /**
     * @var UserManager|null
     */
    protected $userManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $bucket = [];

    /**
     * @required
     * @param RequestStack $requestStack
     * @return $this
     */
    public function setRequestStack(RequestStack $requestStack): self
    {
        $this->requestStack = $requestStack;
        return $this;
    }

    /**
     * @required
     * @param UserManager $userManager
     * @return $this
     */
    public function setUserManager(UserManager $userManager): self
    {
        $this->userManager = $userManager;
        return $this;
    }

    /**
     * @param string $email
     * @return User|null
     */
    private function findOneUserByMail(string $email): ?User
    {
        return $this->em
            ->getRepository(User::class)
            ->findOneByEmail($email)
        ;
    }

    /**
     * @param int $role
     * @param string|null $email
     * @return User
     */
    private function createFakeUser($role = User::ROLE_USER, string $email = null): User
    {
        $faker = Factory::create();

        $user = $this
            ->userManager->create()
            ->setEmail($email ?? $faker->email)
            ->setRoles($role)
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setPhoneNumber($faker->phoneNumber)
            ->setPlainPassword($faker->password(8))
            ->setTimeZone($faker->timezone)
        ;

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param string $type
     * @return User
     */
    private function createFakeUserByType(string $type): User
    {
        switch ($type) {
            case "user":
                return $this->createFakeUser(User::ROLE_USER);
            case "admin":
                return $this->createFakeUser(User::ROLE_ADMIN);
            case "super admin":
                return $this->createFakeUser(User::ROLE_SUPER_ADMIN);
            case "project manager":
                return $this->createFakeUser(User::ROLE_PROJECT_MANAGER);
        }
        throw new RuntimeException('Invalid user type ' . $type);
    }

    /**
     * @When /^an user of type (admin|super admin|project manager|user) saved in (.+)$/
     * @param string $type
     * @param string $path
     */
    public function addFakeUser(string $type, string $path): void
    {
        $user = $this->createFakeUserByType($type);
        $this->accessor->setValue($this->bucket, $path, $user);
    }

    /**
     * @When /^i set my jwt value to (.+)$/
     * @param string $bucket
     * @param string $key
     */
    public function iSetMyJwtTo(string $path): void
    {
        $user = $this->getMe();
        if ($user === null) {
            throw new RuntimeException('No user selected');
        }

        $this->accessor->setValue(
            $this->bucket, $path,
            $this->userManager->getJwt($user)
        );
    }

    /**
     * @When /^i set my refresh token value to (.+)$/
     * @param string $path
     */
    public function iSetMyRefreshTokenTo(string $path): void
    {
        $user = $this->getMe();
        if ($user === null) {
            throw new RuntimeException('No user selected');
        }

        $this->requestStack->push(Request::create('/'));
        $this->accessor->setValue(
            $this->bucket, $path,
            $this->userManager->getRefreshToken($user)
        );
        $this->requestStack->pop();
    }

    /**
     * @When /^i am an user of type (admin|super admin|project manager|user)$/
     * @param string $type
     */
    public function iAmAnUserOfType(string $type): void
    {
        $this->saveMe($this->createFakeUserByType($type));
    }

    /**
     * Save current user
     * @param User $user
     */
    abstract protected function saveMe(User $user): void;

    /**
     * Retrieve saved user
     * @return User|null
     */
    abstract protected function getMe(): ?User;
}