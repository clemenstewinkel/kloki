<?php

namespace App\Repository;

use App\Entity\KloKiEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

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

    public function getQueryFromRequest(Request $request)
    {
        $queryBuilder = $this->createQueryBuilder('event')->innerJoin('event.room', 'room');
        if($request->query->get('room_id'))
            $queryBuilder->andWhere('room.id IN (:roomIds)')->setParameter('roomIds', $request->query->get('room_id'));
        if($request->query->get('art'))
            $queryBuilder->andWhere('event.art IN (:art)')->setParameter('art', $request->query->get('art'));

        if($request->query->get('contractState'))
            $queryBuilder->andWhere('event.contractState IN (:contractState)')->setParameter('contractState', $request->query->get('contractState'));



        if($beginAtAfter = $request->query->get('beginAtAfter'))
            $queryBuilder->andWhere('event.start > :beginAtAfter')->setParameter('beginAtAfter', $beginAtAfter);

        if($beginAtBefore = $request->query->get('beginAtBefore'))
            $queryBuilder->andWhere('event.start < :beginAtBefore')->setParameter('beginAtBefore', $beginAtBefore . ' 23:59:59');

        if($nameContains = $request->query->get('name_contains'))
            $queryBuilder->andWhere('event.name LIKE :nameContains')->setParameter('nameContains', '%' . $nameContains . '%');
        return $queryBuilder;
    }
}
