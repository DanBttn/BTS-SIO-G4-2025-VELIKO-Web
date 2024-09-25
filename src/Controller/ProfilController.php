<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'app_profil', methods: ['GET', 'POST']) ]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Get the logged-in user

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(ProfilFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profil'); // Correct route
        }

        return $this->render('profil/index.html.twig', [
            'profilForm' => $form->createView(),
        ]);
    }


    #[Route('/delete-account', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Get the logged-in user

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            // Rediriger vers la déconnexion après suppression
            return $this->redirectToRoute('app_logout');
        }

        // En cas de problème avec le token CSRF, rediriger avec un message d'erreur
        $this->addFlash('error', 'Problème lors de la suppression du compte.');
        return $this->redirectToRoute('app_profil');
    }
}

