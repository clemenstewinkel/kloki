<?php

namespace App\Repository;

use App\Entity\Addresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Addresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Addresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Addresse[]    findAll()
 * @method Addresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Addresse::class);
    }

    /**
     * @param string $query
     * @param int $limit
     * @return Addresse[]
     */
    public function findAllMatching(string $query, int $limit = 10)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.vorname LIKE :query OR a.nachname LIKE :query OR a.strasse LIKE :query OR a.ort LIKE :query')
            ->setParameter('query', '%' .  $query . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Addresse[] Returns an array of Addresse objects
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
    public function findOneBySomeField($value): ?Addresse
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
