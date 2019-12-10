<?php

namespace App\Repository;

use App\Entity\StageOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StageOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method StageOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method StageOrder[]    findAll()
 * @method StageOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageOrder::class);
    }

    // /**
    //  * @return StageOrder[] Returns an array of StageOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StageOrder
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
