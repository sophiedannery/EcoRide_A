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
        JOIN `user` AS u 
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


    public function findTripById(int $id): array
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
                u.id AS chauffeur_id,
                u.pseudo AS chauffeur,
                v.marque AS vehicule_marque,
                v.modele AS vehicule_modele,
                v.energie AS vehicule_energie
            FROM trajet AS t
            JOIN `user` AS u
                ON t.chauffeur_id = u.id
            JOIN vehicule as v 
                ON t.vehicule_id = v.id
            WHERE t.id = ?
        SQL;

        $trip = $conn->executeQuery($sql, [$id])->fetchAssociative();

        return $trip ?: [];
    }

    public function getTripReviews(int $tripId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT  
            a.note,
            a.commentaire, 
            DATE_FORMAT(a.date_creation, '%Y-%m-%d %H:%i') AS date_creation,
            p.pseudo AS passager
        FROM reservation r
        JOIN avis a
            ON r.id = a.reservation_id
        JOIN `user` p 
            ON r.passager_id = p.id
        WHERE r.trajet_id = ?
            AND a.statut_validation = 'valide'
        ORDER BY a.date_creation DESC
        SQL;

        return $conn->executeQuery($sql, [$tripId])->fetchAllAssociative();
    }

    public function getDriverPreferences(int $chauffeurId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT p.libelle
            FROM preference_user up 
            JOIN preference p 
                ON up.preference_id = p.id
            WHERE up.user_id = ?
        SQL;

        $rows = $conn->executeQuery($sql, [$chauffeurId])->fetchAllAssociative();

        return array_column($rows, 'libelle');
    }

    public function getDriverAverageRating(int $chauffeurId): ?float
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT AVG(a.note) AS avg_rating
        FROM reservation r 
        JOIN avis a 
            ON r.id = a.reservation_id
        JOIN trajet t 
            ON r.trajet_id = t.id
        WHERE t.chauffeur_id = ?
            AND a.statut_validation = 'valide'
        SQL;

        $row = $conn->executeQuery($sql, [$chauffeurId])->fetchAssociative();

        return isset($row['avg_rating']) && $row['avg_rating'] !== null ? (float) $row['avg_rating'] : null;
    }

    public function findNextAvailableTripDate(string $from, string $to, \DateTimeInterface $date): ?\DateTimeImmutable
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT MIN(t.date_depart) AS next_date
        FROM trajet t 
        WHERE t.adresse_depart = ?
            AND t.adresse_arrivee = ?
            AND t.date_depart > ?
            AND t.places_restantes > 0
        SQL;

        $row = $conn->executeQuery($sql, [
            $from,
            $to,
            $date->format('Y-m-d H:i:s'),
        ])->fetchAssociative();

        if (empty($row['next_date'])) {
            return null;
        }

        return new \DateTimeImmutable($row['next_date']);
    }

    public function findTripsByDriver(int $driverId): array
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
            t.statut AS statut_reel,
            IF(t.date_depart > NOW(), 'A venir', 'Passé') AS statut_trajet
        FROM trajet as t 
        WHERE t.chauffeur_id = ?
        AND t.statut <> 'annulé'
        ORDER BY t.date_depart DESC
        SQL;

        return $conn->executeQuery($sql, [$driverId])->fetchAllAssociative();
    }





    public function findCountByDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT 
            DATE(t.date_depart) AS jour,
            COUNT(*) AS total
        FROM trajet t
        WHERE t.statut = 'confirmé'
            AND DATE(t.date_depart) BETWEEN ? AND ?
        GROUP BY DATE (t.date_depart)
        ORDER BY DATE (t.date_depart) ASC
        SQL;

        $rows = $conn->executeQuery($sql, [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
        ])->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['jour']] = (int) $row['total'];
        }

        return $result;
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
