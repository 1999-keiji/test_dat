<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class OutputCondition extends Enum
{
    public const ALL = 1;
    public const WITH_ORDERS = 2;

    protected const ENUM = [
        '全件' => self::ALL,
        '受注有り' => self::WITH_ORDERS
    ];
}
