<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class StatementOfDeliveryPriceShowClass extends Enum
{
    public const NOT_SHOW = '1';
    public const ALL = '2';
    public const UNIT_PRICE_AND_EXCLUDING_TAX = '3';
    public const ONLY_UNIT_PRICE = '4';

    protected const ENUM = [
        '印字しない' => self::NOT_SHOW,
        '単価/税抜額/税額/税込額' => self::ALL,
        '単価/税抜額' => self::UNIT_PRICE_AND_EXCLUDING_TAX,
        '単価' => self::ONLY_UNIT_PRICE
    ];
}
