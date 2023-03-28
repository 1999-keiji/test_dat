<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PaymentTimingDate extends Enum
{
    public const FIFTEENTH = 15;
    public const TWENTYTH = 20;
    public const TWENTY_FIFTH = 25;
    public const END_OF_MONTH = 99;

    protected const ENUM = [
        '15日' => self::FIFTEENTH,
        '20日' => self::TWENTYTH,
        '25日' => self::TWENTY_FIFTH,
        '末日' => self::END_OF_MONTH
    ];
}
