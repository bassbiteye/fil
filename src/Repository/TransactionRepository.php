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

    public function findDate($value, $value1)
    {
        $value = new \DateTime('00:00:00');
        $value1  = new \DateTime('23:59:59');
        return $this->createQueryBuilder('t')
            //->andWhere('t.dateEnvoi <= :val')
            //->andWhere('t.dateRetrait >= :val')
            ->andWhere('t.dateEnvoi >= :val') //du 10
            ->andWhere('t.dateEnvoi <= :val1') //au 12
            ->setParameter('val', $value)
            ->setParameter('val1', $value1)
            ->getQuery()
            ->getResult();
    }

    public function findDatee($value = null, $value1 = null)
    {
        $query = $this->createQueryBuilder('t')
            ->andWhere('t.dateEnvoi >= :val') //du 10
            ->andWhere('t.dateEnvoi <= :val1')
            ->setMaxResults(1);

        if (!is_null($value)) {
            if ($value == 'month') {
                $value = new \DateTime('first day of this month 00:00:00');
                $value1  = new \DateTime('last day of this month 23:59:59');

                $query->andWhere($query->expr()->between('t.dateEnvoi', ':start', ':end'))
                    ->setParameter('start', $value)
                    ->setParameter('end', $value1);
            }
        }

        $entities = $query->getQuery()
            ->getOneOrNullResult();

        return $entities[''];
    }
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
}
