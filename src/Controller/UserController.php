<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/admin/user/{id}/to/editor', name: 'app_user_to_editor')]
    public function changeRole(EntityManagerInterface $entityManager, User $user): Response
    {
        
        $user->setRoles(['ROLE_EDITOR', 'ROLE_USER']);
        $entityManager->flush();

        $this->addFlash('success', 'Le rôle de l\'éditeur a été ajouté à votre utilisateur avec succès !');

        return $this->redirectToRoute('app_user');

        
        
    }
    #[Route('/admin/user/{id}/remove/editor', name: 'app_user_remove_editor')]
    public function removeRole(EntityManagerInterface $entityManager, User $user): Response
    {

        $user->setRoles(['ROLE_USER']);
        $entityManager->flush();

        $this->addFlash('danger', 'Le rôle de l\'éditeur a été retiré de votre utilisateur avec succès !');

        return $this->redirectToRoute('app_user');

        
        
    }
     #[Route('/admin/user/{id}/remove', name: 'app_user_remove')]
    public function userremove(EntityManagerInterface $entityManager, $id, UserRepository $userRepository): Response
    {

        $userFind = $userRepository->find($id);
        $entityManager->remove($userFind);
        $entityManager->flush();

        $this->addFlash('danger', 'L\'utilisateur a été supprimé avec succès !');

        return $this->redirectToRoute('app_user');

        
        
    }
}
