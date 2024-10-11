<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DeleteProfilController extends AbstractController
{
    #[Route('/delete/profil', name: 'app_delete_profil')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Get the logged-in user



        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {

            // Anonymiser les données utilisateur
            $this->anonymizeUser($user, $entityManager);

            // Rediriger vers la déconnexion après anonymisation
            return $this->redirectToRoute('app_logout');
        }
        //$this->redirectToRoute('app_register');

        // En cas de problème avec le token CSRF, rediriger avec un message d'erreur
        $this->addFlash('error', 'Problème lors de la suppression du compte.');

        return $this->render('delete_profil/index.html.twig', [
            'controller_name' => 'DeleteProfilController',
        ]);
    }

    private  function anonymizeUser($user, EntityManagerInterface $entityManager): void
    {
        // Générer une série de 5 chiffres aléatoires
        $randomNumber = random_int( 00000, 99999);

        // Anonymiser les champs sensibles
        $user->setEmail('anonymous_' . $randomNumber . '@veliko.local');
        $user->setNom('anonymous_');
        $user->setPrenom('anonymous_');
        $user->setAdresse('');
        $user->setPassword(md5(random_int(1,10000))); // Réinitialiser le mot de passe

        // Sauvegarder les modifications
        $entityManager->persist($user);
        $entityManager->flush();
    }
}
