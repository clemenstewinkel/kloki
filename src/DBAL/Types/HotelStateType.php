<?php


namespace App\DBAL\Types;


use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class HotelStateType extends AbstractEnumType
{
    public const NONE       = 'none';
    public const NEEDED     = 'needed';
    public const BOOKED     = 'booked';

    protected static $choices = [
        self::NONE       => 'Kein Hotel',
        self::NEEDED     => 'Hotel benötigt',
        self::BOOKED     => 'Hotel gebucht'
    ];
}