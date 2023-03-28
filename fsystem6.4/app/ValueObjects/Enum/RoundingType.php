<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class RoundingType extends Enum
{
    public const FLOOR = 1;
    public const CEIL = 2;
    public const ROUND = 3;

    protected const ENUM = [
        '切り捨て' => self::FLOOR,
        '切り上げ' => self::CEIL,
        '四捨五入' => self::ROUND
    ];
}
