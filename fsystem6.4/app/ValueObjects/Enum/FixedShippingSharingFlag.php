<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class FixedShippingSharingFlag extends Enum
{
    public const COORDINATED = 1;
    public const NOT_COORDINATED = 0;

    protected const ENUM = [
        '連携済' => self::COORDINATED,
        '未連携' => self::NOT_COORDINATED
    ];
}
