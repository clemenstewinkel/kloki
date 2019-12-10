<?php

namespace App\Repository;

use App\Entity\KloKiEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method KloKiEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method KloKiEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method KloKiEvent[]    findAll()
 * @method KloKiEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KloKiEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KloKiEvent::class);
    }

    // /**
    //  * @return KloKiEvent[] Returns an array of KloKiEvent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KloKiEvent
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
