<?php


namespace App\DBAL\Types;


use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ContractStateType extends AbstractEnumType
{
    public const NONE       = 'none';       // Es wird (noch) kein Vertrag benötigt
    public const REQUESTED  = 'requested';  // Der Vertrag soll erstellt werden
    public const SENT       = 'sent';       // Der Vertrag wurde erstellt und verschickt
    public const RECEIVED   = 'received';   // Der Vertrag ist unterschrieben zurückgekommen

    protected static $choices = [
        self::NONE       => 'kein Vertrag',
        self::REQUESTED  => 'Vertrag soll erstellt werden',
        self::SENT       => 'Vertrag wurde versendet',
        self::RECEIVED   => 'Vertrag liegt vor'
    ];
}