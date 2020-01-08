<?php


namespace App\DBAL\Types;


use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventArtType extends AbstractEnumType
{
    public const RENTAL     = 'rental';
    public const SHOW       = 'show';
    public const FAIR       = 'fair';

    protected static $choices = [
        self::RENTAL        => 'Vermietung',
        self::SHOW          => 'Kultur',
        self::FAIR          => 'Markt/Börse/Messe'
    ];
}