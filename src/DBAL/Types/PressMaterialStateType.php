<?php


namespace App\DBAL\Types;


use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PressMaterialStateType extends AbstractEnumType
{
    public const NONE       = 'none';
    public const NEEDED     = 'needed';
    public const AVAILABLE  = 'available';

    protected static $choices = [
        self::NONE       => 'Nein',
        self::NEEDED     => 'Ja',
        self::AVAILABLE  => 'liegt vor'
    ];
}