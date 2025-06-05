<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }


    public function findAvisByChauffeur(int $driverId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT
            a.id AS id_avis,
            a.note AS note,
            a.commentaire AS commentaire, 
            DATE_FORMAT(a.date_creation, '%Y-%m-%d %H:%i') AS date_creation,
            a.statut_validation AS statut_validation,
            u.id AS id_passager,
            u.pseudo as pseudo_passager
        FROM avis AS a
        INNER JOIN reservation AS r ON a.reservation_id= r.id
        INNER JOIN trajet AS t ON r.trajet_id= t.id
        INNER JOIN `user` AS u ON r.passager_id = u.id 
        WHERE t.chauffeur_id = ?
            AND a.statut_validation = 'validé'
        ORDER BY a.date_creation DESC
        SQL;

        return $conn->executeQuery($sql, [$driverId])->fetchAllAssociative();
    }

    //    /**
    //     * @return Avis[] Returns an array of Avis objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Avis
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
