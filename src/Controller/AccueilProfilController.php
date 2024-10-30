<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilProfilController extends AbstractController
{
    #[Route('/accueil/profil', name: 'app_accueil_profil')]
    public function index(): Response
    {

        $id_user = $this->getUser()->getId();

        return $this->render('accueil_profil/index.html.twig', [
            'controller_name' => 'AccueilProfilController',
            'id_user' => $id_user
        ]);
    }
}
