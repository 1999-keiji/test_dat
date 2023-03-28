<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class SlipStatusType extends Enum
{
    public const FIXED_ORDER = 1;
    public const TEMP_ORDER = 2;
    public const RELATION_TEMP_ORDER = 3;

    protected const ENUM = [
        '確定注文' => self::FIXED_ORDER,
        '仮注文' => self::TEMP_ORDER,
        '紐付済仮注文' => self::RELATION_TEMP_ORDER
    ];
}
