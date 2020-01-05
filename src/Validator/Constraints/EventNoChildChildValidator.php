<?php

namespace App\Validator\Constraints;

use App\Entity\KloKiEvent;
use App\Repository\KloKiEventRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EventNoChildChildValidator extends ConstraintValidator
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
        $this->logger->debug('KLOKI: EventNoChildChild called!');
        /** @var KloKiEvent $event */

        if ($event->getParentEvent() && $event->getParentEvent()->getParentEvent()) {
            $this->logger->debug('KLOKI: Parent is a child itself!!');
            $this->context->buildViolation("Das ausgewählte Hauptevent ist selbst ein Unter-Event")
                ->atPath('ParentEvent')
                ->addViolation();
        }

        if ($event->getParentEvent() && $event->getChildEvents()->count() > 0) {
            $this->logger->debug('KLOKI: Event has children itself. Parent can not be assigned!!');
            $this->context->buildViolation("Ein Hauptelement ist nicht erlaubt, weil dieses Event selber Unter-Events hat!")
                ->atPath('ParentEvent')
                ->addViolation();
        }

    }
}