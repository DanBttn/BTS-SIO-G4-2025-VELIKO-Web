<?php

namespace App\Controller;

use App\Entity\StationFavori;
use App\Repository\StationFavoriRepository;
use App\veliko\Api;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavoriController extends AbstractController
{
    #[Route('/favori/{id_user}', name: 'app_favori')]
    public function index(StationFavoriRepository $stationFavoriRepository): Response
    {
        $user = $this->getUser();
        if ($user != null) {
            if ($user->isRenouvelerMdp()) {
                $this->addFlash('error', 'Veuillez renouveler votre mot de passe.');
                return $this->redirectToRoute('app_forced_mdp');
            }
        }

        $id_user = $this->getUser()->getId();
        // Récupérer toutes les stations favorites de l'utilisateur
        $stations = $stationFavoriRepository->findStationsByUser($id_user);
        $stations_fav = [];

        foreach ($stations as $station) {
            $stations_fav[] = [
                'id_station' => $station->getIdStation(),
                'nom_station' => $station->getNomStation()
            ];
        }



        return $this->render('favori/index.html.twig', [
            'controller_name' => 'FavoriController',
            'stations_fav' => $stations_fav
        ]);
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
            $stationFavori->setNomStation($data['station_name']);
            $entityManager->persist($stationFavori);
            $entityManager->flush();
            return new Response('Station ajoutée aux favoris !');
        }
    }

    #[Route('/delete/favori/{id_user}/{id_station}', name: 'app_supp_favori', methods: ['GET'])]
    public function supprimerFavori(int $id_station, int $id_user, EntityManagerInterface $entityManager): Response
    {
        // Cherchez la station favori par son ID et son utilisateur
        $stationFavori = $entityManager->getRepository(StationFavori::class)->findOneBy([
            'id_station' => $id_station,
            'id_user' => $id_user
        ]);

        // Supprimer la station favori
        $entityManager->remove($stationFavori);
        $entityManager->flush();
        $this->addFlash('success', 'Station retirée des favoris !');

        return $this->redirectToRoute('app_favori', ['id_user' => $id_user]);
    }

    #[Route('/stations', name: 'app_stations')]
    public function stations(Api $api, EntityManagerInterface $entityManager): Response
    {
        $stations = $api->getApi('/api/stations', 'GET', "");
        $stations = json_decode($stations, true);

        $stationsNonFavori = [];

        foreach ($stations as $station) {

            // Vérifier si la station est déjà dans les favoris
            $stationFavori = $entityManager->getRepository(StationFavori::class)->findOneBy([
                'id_user' => $this->getUser()->getId(),
                'id_station' => $station['station_id']
            ]);
            if (!$stationFavori){

                $station_datas = [
                    'station_id' => $station['station_id'],
                    'name' => $station['name']
                ];

                $stationsNonFavori[] = $station_datas;
            }

        }



        return $this->render('favori/stations.html.twig', [
            'stationsNonFav' => $stationsNonFavori
        ]);
    }

}
