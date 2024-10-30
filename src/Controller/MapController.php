<?php

namespace App\Controller;

use App\Entity\StationFavori;
use App\veliko\Api;
use Doctrine\ORM\EntityManagerInterface;
//use http\Client\Response;
use http\Env;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]

    public function execute(Request $request, EntityManagerInterface $entityManager, Api $api): Response
    {

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





}
