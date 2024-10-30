<?php

namespace App\Repository;

use App\Entity\StationFavori;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StationFavori>
 */
class StationFavoriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StationFavori::class);
    }


    /**
     * Récupérer toutes les stations favorites d'un utilisateur
     *
     * @param int $id_user
     * @return StationFavori[]
     */
    public function findStationsByUser(int $id_user): array
    {
        return $this->createQueryBuilder('station_fav')
            ->andWhere('station_fav.id_user = :id_user')
            ->setParameter('id_user', $id_user)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return StationFavori[] Returns an array of StationFavori objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StationFavori
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
