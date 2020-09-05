<?php

namespace App\Repository;

use App\Entity\Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reports|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reports|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reports[]    findAll()
 * @method Reports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reports::class);
    }

    public function getPendingAmount()
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r) as pending')
            ->andWhere("r.status = 'Reported'")
            ->getQuery()
            ->getResult()[0]['pending'];
    }

    public function getReports()
    {
        $reports = $this->createQueryBuilder('r')->getQuery()->getResult();
        $reported = [];
        $open = [];
        $closed = [];

        foreach ($reports as $r) {
            if ($r->getStatus() == 'Reported')  $reported[] = $r;
            if ($r->getStatus() == 'Open')      $open[] = $r;
            if ($r->getStatus() == 'Closed')    $closed[] = $r;
        }

        return ['pending' => $reported, 'open' => $open, 'closed' => $closed];
    }

    // /**
    //  * @return Reports[] Returns an array of Reports objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reports
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
