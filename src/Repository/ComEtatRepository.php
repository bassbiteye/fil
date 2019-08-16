<?php

namespace App\Repository;

use App\Entity\ComEtat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ComEtat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComEtat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComEtat[]    findAll()
 * @method ComEtat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComEtatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ComEtat::class);
    }

    // /**
    //  * @return ComEtat[] Returns an array of ComEtat objects
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
    public function findOneBySomeField($value): ?ComEtat
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
