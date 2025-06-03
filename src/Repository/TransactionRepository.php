<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }


    public function getTotalCommissionPlateform(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT COALESCE(SUM(t.montant), 0) AS total_commission
        FROM `transaction` t
        WHERE t.type = ?
        SQL;

        $row = $conn->executeQuery($sql, ['commission_plateforme'])->fetchAssociative();

        return (int) ($row['total_commission'] ?? 0);
    }

    public function findCreditsByDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        SELECT 
            DATE(t.date_transaction) AS jour,
            COALESCE(SUM(t.montant), 0) AS total
        FROM `transaction` t
        WHERE t.type = 'commission_plateforme'
            AND DATE(t.date_transaction) BETWEEN ? AND ?
        GROUP BY DATE (t.date_transaction)
        ORDER BY DATE (t.date_transaction) ASC
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
    //     * @return Transaction[] Returns an array of Transaction objects
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

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
