<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class BasisForRecordingSales extends Enum
{
    public const ORDER = 1;
    public const SHIPMENT = 2;
    public const DELIVERY = 3;
    public const ACCEPTANCE = 4;

    protected const ENUM = [
        '受注基準' => self::ORDER,
        '出荷基準' => self::SHIPMENT,
        '納品基準' => self::DELIVERY,
        '検収基準' => self::ACCEPTANCE,
    ];
}
