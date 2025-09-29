<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail("admin@admin.com");
        $admin->setPassword($this->hashPassword($admin, "admin"));
        $admin->setRoles(["ROLE_ADMIN", "ROLE_EDITOR"]);
        $admin->setFirstName("admin");
        $admin->setLastName("admin");

        $manager->persist($admin);
        $manager->flush();
    }

    public function hashPassword(
        User $user,
        string $plainPassword
    ) {
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }
}
