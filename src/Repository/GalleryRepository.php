<?php

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function getMatchingResults(string $attr)
    {
        if ($attr !== 'uncategorized') {
            return $this->createQueryBuilder('g')
                ->andWhere('c.Name = :attr OR t.Name = :attr')
                ->leftJoin('g.category', 'c')
                ->leftJoin('c.Tags', 't')
                ->setParameter(":attr", $attr)
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('g')
                ->andWhere('g.category IS NULL')
                ->getQuery()
                ->getResult();
        }
    }

    public function getLastId()
    {
        return $this->createQueryBuilder('g')
            ->select('g.id')
            ->setMaxResults(1)
            ->orderBy('g.id', 'DESC')
            ->getQuery()
            ->getResult()[0]['id'];
    }

    public function getNoUploads()
    {
        return (int)$this->createQueryBuilder('g')
            ->select('COUNT(g) as amount')
            ->getQuery()
            ->getResult()[0]['amount'];
    }

    // /**
    //  * @return Gallery[] Returns an array of Gallery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gallery
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
