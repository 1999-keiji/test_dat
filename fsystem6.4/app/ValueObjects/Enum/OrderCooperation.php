<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class OrderCooperation extends Enum
{
    public const COOPERATION     = 1;
    public const NOT_COOPERATION = 0;

    protected const ENUM = [
        '連携する'   => self::COOPERATION,
        '連携しない' => self::NOT_COOPERATION
    ];
}
