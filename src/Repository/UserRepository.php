<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function checkRegister(string $username, string $email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.Username = :username OR u.Email = :email')
            ->setParameter(':username', $username)
            ->setParameter(':email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function getNoUsers()
    {
        return (int)$this->createQueryBuilder('u')
            ->select('COUNT(u) as amount')
            ->getQuery()
            ->getResult()[0]['amount'];
    }

    public function getUsers()
    {
        $all = $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        $admins = [];
        $mods = [];
        $users = [];

        foreach ($all as $u) {
            $temp = $u->getRoles();
            if (in_array('ROLE_ADMIN', $temp)) $admins[] = $u;
            else if (!in_array("ROLE_ADMIN", $temp) && in_array('ROLE_MODERATOR', $temp)) $mods[] = $u;
            else if (!in_array("ROLE_MODERATOR", $temp)) $users[] = $u;
        }
        unset($temp);

        return ['admins' => $admins, 'moderators' => $mods, 'users' => $users];
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
