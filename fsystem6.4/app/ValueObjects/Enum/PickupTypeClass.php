<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PickupTypeClass extends Enum
{
    public const ORDERS_RECEIVED = 'A';

    protected const ENUM = [
        '受注' => self::ORDERS_RECEIVED
    ];
}
