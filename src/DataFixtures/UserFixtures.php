<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class UserFixtures extends Fixture
{
    const EMAIL_ADMIN = 'admin@fixture.fr';
    const EMAIL_SUPER_ADMIN = 'super-admin@fixture.fr';
    const EMAIL_PROJECT_MANAGER = 'project-manager@fixture.fr';
    const EMAIL_USER = 'user@fixture.fr';
    const PASSWORD = 'demopassword';

    /**
     * @var Faker\Generator
     */
    private $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $manager->persist(
            $this->createUser(self::EMAIL_ADMIN, User::ROLE_ADMIN)
        );
        $manager->persist(
            $this->createUser(self::EMAIL_SUPER_ADMIN, User::ROLE_SUPER_ADMIN)
        );
        $manager->persist(
            $this->createUser(self::EMAIL_PROJECT_MANAGER, User::ROLE_PROJECT_MANAGER)
        );
        $manager->persist(
            $this->createUser(self::EMAIL_USER, User::ROLE_USER)
        );
        $manager->flush();
    }

    public function createUser($email, $roles)
    {
        return (new User())
            ->setEmail($email)
            ->setRoles($roles)
            ->setFirstName($this->faker->firstName)
            ->setLastName($this->faker->lastName)
            ->setPhoneNumber($this->faker->phoneNumber)
            ->setPlainPassword(self::PASSWORD)
        ;
    }
}
