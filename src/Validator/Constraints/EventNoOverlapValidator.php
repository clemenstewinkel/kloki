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

        if((!$event->getStart()) || (!$event->getEnd())) return;

        // Wir suchen nach anderen Events mit dem gleichen Raum,
        // die im gewünschten Zeitraum liegen, die also
        // -- beginAt zwischen beginAt und endAt unseres Events ODER
        // -- endAt zwischen beginAt und endAt unseres Events ODER
        // -- beginAt vor unserem beginAt und endAt nach unserem endAt
        $myBeginAt = $event->getStart()->format('Y-m-d H:i');
        $myEndAt   = $event->getEnd()->format('Y-m-d H:i');
        $queryBuilder = $this->eventRepo->createQueryBuilder('event')
            ->innerJoin('event.room', 'room')
            ->andWhere('room.id = ' . $event->getRoom()->getId())
            ->andWhere("
                (event.start > '$myBeginAt' AND event.start < '$myEndAt') OR
                (event.end > '$myBeginAt' AND event.end < '$myEndAt') OR
                (event.start < '$myBeginAt' AND event.end > '$myEndAt')
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