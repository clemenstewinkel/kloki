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
        if($request->query->get('hotelState'))
            $queryBuilder->andWhere('event.hotelState IN (:hotelState)')->setParameter('hotelState', $request->query->get('hotelState'));
        if($request->query->get('pressMaterialState'))
            $queryBuilder->andWhere('event.pressMaterialState IN (:pressMaterialState)')->setParameter('pressMaterialState', $request->query->get('pressMaterialState'));
        if($request->query->get('gemaListState'))
            $queryBuilder->andWhere('event.gemaListState IN (:gemaListState)')->setParameter('gemaListState', $request->query->get('gemaListState'));


        if($request->query->get('state') == 'fixed')
            $queryBuilder->andWhere('event.isFixed = 1');
        if($request->query->get('state') == 'option')
            $queryBuilder->andWhere('event.isFixed = 0');

        if($request->query->get('tech') && in_array('sound', $request->query->get('tech')))
        {
            $queryBuilder->andWhere('event.isTonBenoetigt = 1');
            $queryBuilder->andWhere('event.TonTechniker IS NULL');
        }
        if($request->query->get('tech') && in_array('light', $request->query->get('tech')))
        {
            $queryBuilder->andWhere('event.isLichtBenoetigt = 1');
            $queryBuilder->andWhere('event.LichtTechniker IS NULL');
        }

        if($request->query->get('ba') == 'avail')
            $queryBuilder->andWhere('event.stageOrder IS NOT NULL');
        if($request->query->get('ba') == 'miss')
            $queryBuilder->andWhere('event.stageOrder IS NULL');



        if($beginAtAfter = $request->query->get('beginAtAfter'))
            $queryBuilder->andWhere('event.start > :beginAtAfter')->setParameter('beginAtAfter', $beginAtAfter);

        if($beginAtBefore = $request->query->get('beginAtBefore'))
            $queryBuilder->andWhere('event.start < :beginAtBefore')->setParameter('beginAtBefore', $beginAtBefore . ' 23:59:59');

        if($nameContains = $request->query->get('name_contains'))
            $queryBuilder->andWhere('event.name LIKE :nameContains')->setParameter('nameContains', '%' . $nameContains . '%');

        $queryBuilder->orderBy('event.start', 'ASC');
        return $queryBuilder;
    }
}
