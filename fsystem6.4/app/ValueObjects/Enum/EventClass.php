<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class EventClass extends Enum
{
    public const BANK_HOLIDAY = 1;
    public const NATIONAL_HOLIDAY = 2;

    protected const ENUM = [
        '銀行休業日' => self::BANK_HOLIDAY,
        '祝日' => self::NATIONAL_HOLIDAY,
    ];
}
