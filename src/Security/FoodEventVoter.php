<?php


namespace App\Security;

use App\DBAL\Types\ContractStateType;
use App\Entity\KloKiEvent;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class FoodEventVoter extends Voter
{
    const EDIT = 'EVENT_EDIT';
    const DELETE = 'EVENT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE, self::EDIT])) {
            return false;
        }

        // only vote on `KloKiEvent` objects
        if (!$subject instanceof KloKiEvent) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        if($this->security->isGranted('ROLE_ADMIN'))
        {
            // ADMIN is always allowed to edit and delete!
            return true;
        }
        if(!$this->security->isGranted('ROLE_FOOD'))
        {
            // ROLE_FOOD is the only one except ADMIN to edit or delete!
            return false;
        }
        // ROLE_FOOD for sure at this point!

        // $subject is of type KloKiEvent. We know thanks to supports()
        /** @var $event KloKiEvent */
        $event = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($event);
            case self::DELETE:
                return $this->canDelete($event);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canEdit(KloKiEvent $event)
    {
        // Ein Event darf von ROLE_FOOD nur geändert werden, wenn es auch von ROLE_FOOD angelegt worden ist.
        return ($event->getCreatedBy() && in_array('ROLE_FOOD', $event->getCreatedBy()->getRoles()));
    }

    private function canDelete(KloKiEvent $event)
    {
        // ROLE_FOOD kann Event löschen,
        // - wenn es von ROLE_FOOD angelegt wurde UND
        // - wenn der Status noch auf OPTION steht UND
        // - wenn der Vertrags-Status noch auf NONE oder REQUESTED steht.
        return (($event->getCreatedBy() && in_array('ROLE_FOOD', $event->getCreatedBy()->getRoles())) &&
                (!$event->getIsFixed()) &&
                (in_array($event->getContractState(), [ContractStateType::NONE, ContractStateType::REQUESTED]))
               );
    }

}