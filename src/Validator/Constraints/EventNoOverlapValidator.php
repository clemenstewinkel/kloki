<?php

namespace App\Validator\Constraints;

use App\Entity\KloKiEvent;
use App\Repository\KloKiEventRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EventNoOverlapValidator extends ConstraintValidator
{
    private $eventRepo;

    public function __construct(KloKiEventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function validate($event, Constraint $constraint)
    {
        /** @var KloKiEvent $event */
        // Wir suchen nach anderen Events mit dem gleichen Raum,
        // die im gewünschten Zeitraum liegen, die also
        // -- beginAt zwischen beginAt und endAt unseres Events ODER
        // -- endAt zwischen beginAt und endAt unseres Events ODER
        // -- beginAt vor unserem beginAt und endAt nach unserem endAt
        $collidingEvents = $this->eventRepo->findBy(['room' => $event->getRoom()]);

        if ($event->getName() != 'Tuppes') {
            $this->context->buildViolation("So nicht mein Freund, dein Wert war: {{ string }}")
                ->setParameter('{{ string }}', $event->getBeginAt()->format('Y-m-d H:i'))
                ->atPath('startDate')
                ->addViolation();
        }
    }
}