<?php


namespace App\DBAL\Types;


use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PressMaterialStateType extends AbstractEnumType
{
    public const NONE       = 'none';
    public const NEEDED     = 'needed';
    public const AVAILABLE  = 'available';

    protected static $choices = [
        self::NONE       => 'Kein Pressematerial nötig',
        self::NEEDED     => 'Pressematerial muss noch besorgt werden',
        self::AVAILABLE  => 'Pressematerial liegt vor'
    ];
}