<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ShipmentWayClass extends Enum
{
    public const SHIPMENT = '3';
    public const DIRECT_SHIPMENT = '5';
    public const COLLECTION = '6';
    public const WAREHOUSE = '8';
    public const OTHERS = '9';

    protected const ENUM = [
        '発送' => self::SHIPMENT,
        '直送' => self::DIRECT_SHIPMENT,
        '営業引取' => self::COLLECTION,
        '倉庫' => self::WAREHOUSE,
        'その他' => self::OTHERS
    ];
}
