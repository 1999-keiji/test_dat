<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class AbroadShipmentPriceShowClass extends Enum
{
    public const NOT_PRINT = 1;
    public const PRINT = 2;

    protected const ENUM = [
        '印字しない' => self::NOT_PRINT,
        '印字する' => self::PRINT
    ];
}
