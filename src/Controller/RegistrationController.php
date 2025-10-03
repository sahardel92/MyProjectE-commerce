<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
         // hna kancréé user jdid (vide)
        $user = new User();
         // hna kancréé formulaire dial inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);// hna kandir traitement dial formulaire

        // ila formulaire tsift o valid
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // hna kanhashi (kancrypte) password dial user
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // save user f database
            $entityManager->persist($user);
            $entityManager->flush();

            // hna t9der tzid actions khourin (exemple: b3at email de bienvenue)
            // login automatique ba3d l’inscription

            return $security->login($user, SecurityAuthenticator::class, 'main');
        }
         // hna kan afficher formulaire d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
