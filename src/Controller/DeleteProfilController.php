<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

class DeleteProfilController extends AbstractController
{
    #[Route('/delete/profil', name: 'app_delete_profil')]
    public function index(Request $request, EntityManagerConfig $entityManager): Response
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

        return $this->render('delete_profil/index.html.twig', [
            'controller_name' => 'DeleteProfilController',
        ]);
    }
}
