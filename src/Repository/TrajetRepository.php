<?php

namespace App\Repository;

use App\Entity\Trajet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trajet>
 */
class TrajetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trajet::class);
    }

    public function searchTrips(string $from, string $to, \DateTimeInterface $date, bool $eco = false, ?int $maxPrice = null, ?int $maxDuration = null, ?float $minRating = null): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL

        SELECT
            t.id AS id_trajet, 
            t.adresse_depart, 
            t.adresse_arrivee, 
            DATE_FORMAT(t.date_depart, '%Y-%m-%d %H:%i') AS date_depart, 
            DATE_FORMAT(t.date_arrivee, '%Y-%m-%d %H:%i') AS date_arrivee, 
            t.prix,
            t.places_restantes, 
            u.pseudo AS chauffeur,
            COALESCE(ar.avg_rating, 0) AS avg_rating,
            v.energie AS energie
        FROM trajet AS t
        JOIN user AS u 
            ON t.chauffeur_id = u.id
        JOIN vehicule as v 
            ON t.vehicule_id = v.id
        
        LEFT JOIN (
            SELECT 
                t2.chauffeur_id,
                AVG(a.note) AS avg_rating
            FROM reservation r2
            JOIN avis a 
                ON r2.id = a.reservation_id
            JOIN trajet t2
                ON r2.trajet_id = t2.id
            WHERE a.statut_validation = 'validé'
            GROUP BY t2.chauffeur_id 
        ) AS ar 
            ON ar.chauffeur_id = t.chauffeur_id 

        WHERE 
            t.adresse_depart = ?
            AND t.adresse_arrivee = ?
            AND DATE(t.date_depart) = ?
            AND t.places_restantes > 0
        SQL;

        $params = [
            $from,
            $to,
            $date->format('Y-m-d'),
        ];

        if ($eco) {
            $sql .= " AND v.energie = 'electrique'";
        }

        if ($maxPrice !== null) {
            $sql .= " AND t.prix <= ?";
            $params[] = $maxPrice;
        }

        if ($maxDuration !== null) {
            $sql .= " AND TIMESTAMPDIFF(MINUTE, t.date_depart, t.date_arrivee) <= ?";
            $params[] = $maxDuration;
        }

        if ($minRating !== null) {
            $sql .= " AND COALESCE(ar.avg_rating, 0) >= ?";
            $params[] = $minRating;
        }

        $sql .= " ORDER BY t.date_depart";

        return $conn->executeQuery($sql, $params)->fetchAllAssociative();
    }

    //    /**
    //     * @return Trajet[] Returns an array of Trajet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trajet
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
