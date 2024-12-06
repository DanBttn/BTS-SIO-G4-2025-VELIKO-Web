<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\StationFavori;
use App\Form\PasswordChangeFormType;
use App\Form\ReservationFormType;
use App\Form\ResetFormType;
use App\veliko\Api;
use Doctrine\ORM\EntityManagerInterface;
//use http\Client\Response;
use http\Env;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function execute(Request $request, EntityManagerInterface $entityManager, Api $api): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur actuel

        if ($user!=null)
        {
        if ($user->isRenouvelerMdp()) {
            $this->addFlash('error', 'Veuillez renouveler votre mot de passe.');
            return $this->redirectToRoute('app_forced_mdp');
        }
        }
        $responseStation = $api->getApi("/api/stations");
        $responseStation =  json_decode($responseStation,true);


        $responseStationStatus = $api->getApi("/api/stations/status");
        $responseStationStatus =  json_decode($responseStationStatus,true);



        $user = $this->getUser(); // Récupérer l'utilisateur actuel
        $favoris = [];
        $favorisIds = [];

        // Récupérer les favoris de l'utilisateur si connecté
        if ($user) {
            $favoris = $entityManager->getRepository(StationFavori::class)->findBy([
                'id_user' => $user->getId(),
            ]);
            $favorisIds = array_map(fn($fav) => $fav->getIdStation(), $favoris); // Récupérer les IDs des stations favoris
        }

        $stations = [];

        foreach ($responseStation as $station1) {
            //$stations_data = [];

            foreach ($responseStationStatus as $station2) {
                if ($station1['station_id'] == $station2['station_id']) {

                    $isFavori = in_array($station1['station_id'], $favorisIds); // Vérifier si la station est un favori


                    $stations_data = [
                        "station_id" => $station1['station_id'],
                        "name" => $station1['name'],
                        "lon" => $station1['lon'],
                        "lat" => $station1['lat'],
                        "velo_electrique" => $station2['num_bikes_available_types'][1]['ebike'],
                        "velo_mecanique" => $station2['num_bikes_available_types'][0]['mechanical'],
                        "capacite" => $station1['capacity'],
                        "isFavori" => $isFavori,
                    ];

                    $stations[] = $stations_data;
                }
            }
        }

        return $this->render('map/index.html.twig',
            [
                "titre"   => 'MapController',
                "request" => $request,
                "stations" => $stations
            ]
        );
    }



    #[Route('/reservation', name: 'app_reservation', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();

        // Récupérer les données du formulaire
        $stationDep = $request->request->get('departure');
        $stationFin = $request->request->get('destination');

        // Valider les données
        if (empty($stationDep) || empty($stationFin)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs.');
        }
        else if ($stationDep === $stationFin) {
            $this->addFlash('error', 'La station de départ et la station de destination ne peuvent pas être identiques.');
        }
        else {
            // Créer une nouvelle réservation
            $reservation = new Reservation();
            $reservation->setDateResa(new \DateTime('now'));
            $reservation->setStationDep($stationDep);
            $reservation->setStationFin($stationFin);
            $reservation->setIdUser($user->getId());

            // Sauvegarder la réservation
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Ajouter un message de confirmation
            $this->addFlash('success', sprintf(
                'Votre réservation de <b>%s</b> à <b>%s</b> a été enregistrée avec succès.',
                $reservation->getStationDep(),
                $reservation->getStationFin()
            ));


        }

        return $this->redirectToRoute('app_map');
    }

    #[Route('/forced', name: 'app_forced_mdp')]
    public function forced(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour changer votre mot de passe.');
        }

        $form = $this->createForm(PasswordChangeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Obtenez le nouveau mot de passe en clair
            $plainPassword = $form->get('new_password')->getData();
            $confirmPassword = $form->get('confirm_password')->getData();

            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_forced_mdp');
            }
            $currentPassword = $form->get('current_password')->getData();
            if ($plainPassword == $currentPassword) {
                $this->addFlash('danger', 'Le nouveau mot de passe ne peut pas être le même que l\'ancien.');
                return $this->redirectToRoute('app_forced_mdp');
            }
            // Hachez le mot de passe et mettez-le à jour
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Mettre à jour renouvelerMdp à false
            $user->setRenouvelerMdp(false);

            // Persister les changements
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');

            // Redirigez vers une autre page, comme la page d'accueil
            return $this->redirectToRoute('app_map');
        }

        return $this->render('security/forced.html.twig', [
            'form' => $form->createView(),
        ]);
    }




}
