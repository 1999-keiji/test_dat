<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ShipmentStatus extends Enum
{
    public const UNSHIPPED = 0;
    public const SHIPPED = 1;

    protected const ENUM = [
        '未出荷' => self::UNSHIPPED,
        '出荷済' => self::SHIPPED
    ];
}
