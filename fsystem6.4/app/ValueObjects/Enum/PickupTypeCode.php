<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PickupTypeCode extends Enum
{
    public const NORMAL = '01';
    public const PAID_PAYMENT = '07';
    public const MAINTENANCE = '10';
    public const ANCIENT = '31';

    protected const ENUM = [
        '通常' => self::NORMAL,
        '有償支給' => self::PAID_PAYMENT,
        '保守' => self::MAINTENANCE,
        '無代' => self::ANCIENT
    ];
}
