<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PaymentTimingMonth extends Enum
{
    public const CURRENT_MONTH = 0;
    public const NEXT_MONTH = 1;
    public const MONTH_AFTER_NEXT = 2;

    protected const ENUM = [
        '当月' => self::CURRENT_MONTH,
        '翌月' => self::NEXT_MONTH,
        '翌々月' => self::MONTH_AFTER_NEXT
    ];
}
