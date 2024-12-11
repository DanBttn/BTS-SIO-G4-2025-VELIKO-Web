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
        $user = $this->getUser();
        if ($user->isRenouvelerMdp()) {
            $this->addFlash('error', 'Veuillez renouveler votre mot de passe.');
            return $this->redirectToRoute('app_forced_mdp');
        }// Obtenir l'utilisateur connecté

        // Vérifier si le formulaire a été soumis (c'est-à-dire si un token est présent dans la requête)
        if ($request->request->has('_token')) {

            // Vérifier le token CSRF
            if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {

                // Anonymiser les données utilisateur
                $this->anonymizeUser($user, $entityManager);

                // Rediriger vers la déconnexion après anonymisation
                return $this->redirectToRoute('app_logout');
            }

            // Ajouter un message d'erreur seulement si le token est incorrect
            $this->addFlash('error', 'Problème lors de la suppression du compte.');
        }

        // Afficher la page de confirmation de suppression sans message d'erreur
        return $this->render('delete_profil/index.html.twig', [
            'controller_name' => 'DeleteProfilController',
        ]);
    }

    private function anonymizeUser($user, EntityManagerInterface $entityManager): void
    {
        // Générer une série de 5 chiffres aléatoires
        $randomNumber = random_int(0, 99999);

        // Anonymiser les champs sensibles
        $user->setEmail('anonymous_' . $randomNumber . '@veliko.local');
        $user->setNom('anonymous_');
        $user->setPrenom('anonymous_');
        $user->setAdresse('');
        $user->setPassword(md5(random_int(1, 10000))); // Réinitialiser le mot de passe
        $user->setConfirmationToken(null); // Supprimer le token de confirmation
        $user->isVerified(false); // Marquer le compte comme non vérifié

        // Sauvegarder les modifications
        $entityManager->persist($user);
        $entityManager->flush();
    }
}
