<?php

namespace App\Repository;

use App\Entity\ComProprietaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ComProprietaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComProprietaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComProprietaire[]    findAll()
 * @method ComProprietaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComProprietaireRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ComProprietaire::class);
    }

    // /**
    //  * @return ComProprietaire[] Returns an array of ComProprietaire objects
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
    public function findOneBySomeField($value): ?ComProprietaire
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
