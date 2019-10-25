<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */


    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getByDate($debut, $fin, $user)
    {
        $from = new \DateTime($debut->format("Y-m-d") . " 00:00:00");
        $to   = new \DateTime($fin->format("Y-m-d") . " 23:59:59");

        $qb = $this->createQueryBuilder("t");
        $qb
            ->andWhere('t.dateEnvoi BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('t.user = :val')
            ->setParameter('val', $user);
        $result = $qb->getQuery()->getResult();

        return $result;
    }
    public function finByDateR($debut, $fin, $user)
    {
        $from = new \DateTime($debut->format("Y-m-d") . " 00:00:00");
        $to   = new \DateTime($fin->format("Y-m-d") . " 23:59:59");

        $qb = $this->createQueryBuilder("t");
        $qb
            ->andWhere('t.dateRetrait BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('t.userRetrait = :val')
            ->setParameter('val', $user);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function findEnP($debut, $fin, $user)
    {
        $from = new \DateTime($debut->format("Y-m-d") . " 00:00:00");
        $to   = new \DateTime($fin->format("Y-m-d") . " 23:59:59");
        return $this->createQueryBuilder("t")
            ->innerJoin('t.user', 'u')
            ->andWhere('t.dateEnvoi BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->addSelect('u')
            ->andWhere('u.partenaire = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();
    }
    public function findRetP($debut, $fin, $user)
    {
        $from = new \DateTime($debut->format("Y-m-d") . " 00:00:00");
        $to   = new \DateTime($fin->format("Y-m-d") . " 23:59:59");
        return $this->createQueryBuilder("t")
            ->innerJoin('t.userRetrait', 'u')
            ->andWhere('t.dateRetrait BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->addSelect('u')
            ->andWhere('u.partenaire = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();
    }

    public function getCount($user)
    {
        return $this->createQueryBuilder('t')
			->select('COUNT (t.id)')
            ->where('t.user = :val')
            ->andwhere('t.userRetrait = :val')
            ->setParameter('val', $user)
          ->getQuery()
          ->getResult();
	}
}
