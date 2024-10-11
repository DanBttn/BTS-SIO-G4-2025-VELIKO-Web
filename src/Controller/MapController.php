<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]

        public function execute(Request $request): Response
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

        $responseStations = curl_exec($curl1);
        $err1 = curl_error($curl1);

        curl_close($curl1);

        $responseStations =  json_decode($responseStations,true);




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

        $responseStationsStatus = curl_exec($curl2);
        $err2 = curl_error($curl2);

        curl_close($curl2);

        $responseStationsStatus =  json_decode($responseStationsStatus,true);

        $stations = [];



        foreach ($responseStations as $station1) {
            $stations_data = [];

            foreach ($responseStationsStatus as $station2) {
                if ($station1['station_id'] == $station2['station_id']) {

                    $stations_data = [
                        "name" => $station1['name'],
                        "lon" => $station1['lon'],
                        "lat" => $station1['lat'],
                        "velo_electrique" => $station2['num_bikes_available_types'][1]['ebike'],
                        "velo_mecanique" => $station2['num_bikes_available_types'][0]['mechanical'],
                        "capacite" => $station1['capacity']
                    ];

                    $stations[] = $stations_data;
                }
            }
        }


        //var_dump($stations);

        //var_dump($response2['data']['stations'][0]['num_bikes_available_types'][1]['ebike']);






        return $this->render('map/index.html.twig',
            [
                "titre"   => 'MapController',
                "request" => $request,
                "stations" => $stations
            ]
        );
    }

}
