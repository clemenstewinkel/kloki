<?php

namespace App\Repository;

use App\Entity\KloKiEventType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method KloKiEventType|null find($id, $lockMode = null, $lockVersion = null)
 * @method KloKiEventType|null findOneBy(array $criteria, array $orderBy = null)
 * @method KloKiEventType[]    findAll()
 * @method KloKiEventType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KloKiEventTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KloKiEventType::class);
    }

    // /**
    //  * @return KloKiEventType[] Returns an array of KloKiEventType objects
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
    public function findOneBySomeField($value): ?KloKiEventType
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
