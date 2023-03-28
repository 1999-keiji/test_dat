<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class DeliveryDestinationClass extends Enum
{
    public const DOMESTIC = 1;
    public const ABROAD = 2;
    public const WAREHOUSE = 3;
    public const AGENT = 4;

    protected const ENUM = [
        '納入先(国内)' => self::DOMESTIC,
        '納入先(海外)' => self::ABROAD,
        '倉庫' => self::WAREHOUSE,
        '引取業者' => self::AGENT
    ];
}
