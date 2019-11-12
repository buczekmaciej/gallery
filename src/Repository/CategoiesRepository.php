<?php

namespace App\Repository;

use App\Entity\Categoies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Categoies|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categoies|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categoies[]    findAll()
 * @method Categoies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoies::class);
    }

    // /**
    //  * @return Categoies[] Returns an array of Categoies objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Categoies
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
