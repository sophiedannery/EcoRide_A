<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\CssSelector\Node\PseudoNode;

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
            t.statut AS statut_trajet,
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

    public function findPassengerPseudoByTrajet(int $trajetId): array
    {
        $conn = $this->getEntityManager()->getConnection();


        $sql = <<<'SQL'
SELECT u.pseudo 
FROM reservation  r  
JOIN `user` u ON r.passager_id = u.id 
WHERE r.trajet_id = ?
SQL;

        $rows = $conn->executeQuery($sql, [$trajetId])->fetchAllAssociative();

        return array_column($rows, 'pseudo');
    }


    public function findSignaledReservations(): array
    {
        $conn = $this->getEntityManager()->getConnection();


        $sql = <<<'SQL'
SELECT 
        r.id as reservation_id,
        r.commentaire_probleme AS commentaire_probleme,
        t.id AS trajet_id,
        t.adresse_depart AS adresse_depart,
        t.adresse_arrivee AS adresse_arrivee,
        t.date_depart AS date_depart,
        t.date_arrivee AS date_arrivee,
        p.pseudo AS passager_pseudo,
        p.email AS passager_email,
        c.pseudo AS chauffeur_pseudo,
        c.email AS chauffeur_email,
        r.date_confirmation AS date_confirmation
    FROM reservation r
        JOIN trajet t ON r.trajet_id = t.id 
        JOIN `user` p ON r.passager_id = p.id
        JOIN `user` c ON t.chauffeur_id = c.id 
    WHERE r.statut = 'signalé'
    ORDER BY t.date_depart DESC
SQL;

        $rows = $conn->executeQuery($sql)->fetchAllAssociative();

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
