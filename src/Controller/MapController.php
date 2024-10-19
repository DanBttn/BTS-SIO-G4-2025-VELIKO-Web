<?php

namespace App\Controller;

use App\Entity\StationFavori;
use Doctrine\ORM\EntityManagerInterface;
//use http\Client\Response;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]

    public function execute(Request $request, EntityManagerInterface $entityManager): Response
    {



        $curl1 = curl_init();

        curl_setopt_array($curl1, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/api/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false

        ]);

        $responseStation = curl_exec($curl1);
        $err1 = curl_error($curl1);

        curl_close($curl1);

        $responseStation =  json_decode($responseStation,true);


        $curl2 = curl_init();

        curl_setopt_array($curl2, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/api/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $responseStationStatus = curl_exec($curl2);
        $err2 = curl_error($curl2);

        curl_close($curl2);

        $responseStationStatus =  json_decode($responseStationStatus,true);


        $user = $this->getUser(); // Récupérer l'utilisateur actuel
        $favoris = [];

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


    #[Route('/ajout/favori', name: 'app_ajout_favori', methods: ['POST'])]
    public function ajoutFavori(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Récupérer les données JSON envoyées via la requête AJAX
        $data = json_decode($request->getContent(), true);
        $stationId = $data['station_id'];



        // Vérifier si la station est déjà dans les favoris
        $stationFavori = $entityManager->getRepository(StationFavori::class)->findOneBy([
            'id_user' => $user->getId(),
            'id_station' => $stationId
        ]);

        if ($stationFavori) {
            // Si la station est déjà un favori, la supprimer
            $entityManager->remove($stationFavori);
            $entityManager->flush();
            return new Response('Station retirée des favoris !');
        } else {
            // Sinon, ajouter la station aux favoris
            $stationFavori = new StationFavori();
            $stationFavori->setIdUser($user->getId());
            $stationFavori->setIdStation($stationId);
            $entityManager->persist($stationFavori);
            $entityManager->flush();
            return new Response('Station ajoutée aux favoris !');
        }
    }



}
