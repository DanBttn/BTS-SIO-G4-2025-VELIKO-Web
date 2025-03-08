<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\veliko\GenerateToken;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        //c'est normal si c'est vide
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("admin@mail.dev");
        $user->setPassword($this->hasher->hashPassword($user, 'adminBonjour1234!!'));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setAdresse("12 rue chezMoi");
        $user->setNom("Admin");
        $user->setCodePostal("92200");
        $user->setVille("Paris");
        $user->setPrenom("Admin");
        $date=new \DateTime("2025-01-01");
        $user->setDateNaiss($date);
        $user->setIsVerified(true);
        $user->setBlocked(false);
        $user->setRenouvelerMdp(false);

        $manager->persist($user);


        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user-$i@veliko.com");
            $user->setPassword($this->hasher->hashPassword($user, "password"));
            $user->setNom("User $i");
            $user->setPrenom("User $i");
            $user->setDateNaiss(new \DateTime('now'));
            $user->setCodePostal("12345");
            $user->setVille("Paris");
            $user->setAdresse("1 rue de Paris");
            $user->setConfirmationToken((new GenerateToken())->create());
            $user->setIsVerified(true);
            $user->setBlocked(false);
            $user->setRenouvelerMdp(false);


            $manager->persist($user);
            $manager->flush();

            $reservation = new Reservation();
            $reservation->setIdUser($user->getId());
            $reservation->setDateResa(new \DateTime('now'));
            $reservation->setStationDep("Benjamin Godard - Victor Hugo");
            $reservation->setStationFin("Rouget de L'isle - Watteau");
            $reservation->setTypeVelo("électrique");
            $manager->persist($reservation);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $manager->flush();
    }
}