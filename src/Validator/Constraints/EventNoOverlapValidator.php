<?php

namespace App\Validator\Constraints;

use App\Entity\KloKiEvent;
use App\Repository\KloKiEventRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EventNoOverlapValidator extends ConstraintValidator
{
    private $eventRepo;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger,  KloKiEventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
        $this->logger = $logger;
    }

    public function validate($event, Constraint $constraint)
    {
        $this->logger->debug('KLOKI: EventNoOverlapValidator called!');
        /** @var KloKiEvent $event */

        if((!$event->getBeginAt()) || (!$event->getEndAt())) return;

        // Wir suchen nach anderen Events mit dem gleichen Raum,
        // die im gewünschten Zeitraum liegen, die also
        // -- beginAt zwischen beginAt und endAt unseres Events ODER
        // -- endAt zwischen beginAt und endAt unseres Events ODER
        // -- beginAt vor unserem beginAt und endAt nach unserem endAt
        $myBeginAt = $event->getBeginAt()->format('Y-m-d H:i');
        $myEndAt   = $event->getEndAt()->format('Y-m-d H:i');
        $queryBuilder = $this->eventRepo->createQueryBuilder('event')
            ->innerJoin('event.room', 'room')
            ->andWhere('room.id = ' . $event->getRoom()->getId())
            ->andWhere("
                (event.beginAt > '$myBeginAt' AND event.beginAt < '$myEndAt') OR
                (event.endAt > '$myBeginAt' AND event.endAt < '$myEndAt') OR
                (event.beginAt < '$myBeginAt' AND event.endAt > '$myEndAt')
            ")
            ->setMaxResults(1)
        ;
        if($event->getId()) $queryBuilder->andWhere("event.id <> " . $event->getId());
        $collidingEvents = $queryBuilder->getQuery()->getResult();

        if ($collidingEvents) {
            $this->context->buildViolation("In diesem Zeitraum existieren schon ein oder mehrere Events in diesem Raum.")
                //->setParameter('{{ room }}', count($collidingEvents))
                ->atPath('endDate')
                ->addViolation();

            $this->context->buildViolation("In diesem Zeitraum existieren schon ein oder mehrere Events in diesem Raum.")
                //->setParameter('{{ room }}', count($collidingEvents))
                ->atPath('room')
                ->addViolation();

        }
    }
}