<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Returns an array of User objects
     */

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }



    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getUserSystem()
    {
        return $this->createQueryBuilder('u')
            ->where('u.partenaire IS NULL')
            ->getQuery()
            ->getResult();
    }
    public function getUserPart($user)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.partenaire = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();

       
    }
    public function getInfo($user,$id)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.id = :val1')
            ->setParameter('val1', $id)
            ->andWhere('u.Compte = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();

       
    }
    
}
