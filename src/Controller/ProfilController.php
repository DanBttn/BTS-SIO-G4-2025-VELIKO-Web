<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'app_profil', methods: ['GET', 'POST']) ]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        if ($user->isRenouvelerMdp()) {
            $this->addFlash('error', 'Veuillez renouveler votre mot de passe.');
            return $this->redirectToRoute('app_forced_mdp');
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
}

