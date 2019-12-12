<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EventNoOverlap extends Constraint
{
    public $pessage = "Na du doof...?";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}