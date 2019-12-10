<?php

namespace App\Repository;

use App\Entity\Ausstattung;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ausstattung|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ausstattung|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ausstattung[]    findAll()
 * @method Ausstattung[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AusstattungRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ausstattung::class);
    }

    // /**
    //  * @return Ausstattung[] Returns an array of Ausstattung objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ausstattung
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
