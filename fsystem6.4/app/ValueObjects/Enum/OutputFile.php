<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class OutputFile extends Enum
{
    public const SHIPPING_INFO = 1;
    public const NOTE_RECEIPT = 2;

    protected const ENUM = [
        '出荷案内書' => self::SHIPPING_INFO,
        '納品書・受領書' => self::NOTE_RECEIPT
    ];
}
