<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findHistoryByUser(int $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<'SQL'
        SELECT r.id AS reservation_id,
            DATE_FORMAT(r.date_confirmation, '%Y-%m-%d %H:%i') AS date_confirmation,
            r.statut AS reservation_statut,
            r.credits_utilises AS credits_utilises,
            t.id AS trajet_id,
            t.adresse_depart,
            t.adresse_arrivee,
            DATE_FORMAT(t.date_depart, '%Y-%m-%d %H:%i') AS date_depart,
            DATE_FORMAT(t.date_arrivee, '%Y-%m-%d %H:%i') AS date_arrivee,
            t.prix,
            u.pseudo AS chauffeur_pseudo
        FROM reservation as r 
        JOIN trajet AS t ON r.trajet_id = t.id 
        JOIN `user` AS u ON t.chauffeur_id = u.id 
        WHERE r.passager_id = ?
        ORDER BY r.date_confirmation DESC
        SQL;

        $rows = $conn->executeQuery($sql, [$userId])->fetchAllAssociative();

        return $rows;
    }




    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
