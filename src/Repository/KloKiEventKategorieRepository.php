<?php

namespace App\Repository;

use App\Entity\KloKiEventKategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method KloKiEventKategorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method KloKiEventKategorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method KloKiEventKategorie[]    findAll()
 * @method KloKiEventKategorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KloKiEventKategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KloKiEventKategorie::class);
    }

    // /**
    //  * @return KloKiEventKategorie[] Returns an array of KloKiEventKategorie objects
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
    public function findOneBySomeField($value): ?KloKiEventKategorie
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
