<?php

namespace App\Repository;

use App\Entity\Bestuhlungsplan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Bestuhlungsplan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bestuhlungsplan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bestuhlungsplan[]    findAll()
 * @method Bestuhlungsplan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BestuhlungsplanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestuhlungsplan::class);
    }

    // /**
    //  * @return Bestuhlungsplan[] Returns an array of Bestuhlungsplan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bestuhlungsplan
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
