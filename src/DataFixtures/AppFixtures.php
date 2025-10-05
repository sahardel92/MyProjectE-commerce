<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    // object zdnha bach ndiro hash l password
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        // dependency injection dyal passsword hasher
        // ya3ni khchina no3 dyal l hasher f l class dyalna
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // fixture bach n9ado new admin user f lwl dyal base de donnee
        $admin = new User();
        $admin->setEmail("admin@admin.com");
        // drna had l password b dak l l function dyal l hashing
        $admin->setPassword($this->hashPassword($admin, "admin"));
        $admin->setRoles(["ROLE_ADMIN", "ROLE_EDITOR"]);
        $admin->setFirstName("admin");
        $admin->setLastName("admin");

        // glna l doctrine tsjl had entity 3ndha
        $manager->persist($admin);
        // glna l doctrine tsjl new user f database
        $manager->flush();
    }

    // hashing function
    // katakhd useer o l password o kadir lih hashing w katrj3o
    public function hashPassword(
        User $user,
        string $plainPassword
    ) {
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }
}
