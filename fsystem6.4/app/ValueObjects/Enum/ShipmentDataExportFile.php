<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ShipmentDataExportFile extends Enum
{
    public const BY_DAY = 1;
    public const BY_CUSTOMER = 2;

    protected const ENUM = [
        '日別製品化率・出荷重量一覧' => self::BY_DAY,
        '顧客別出荷実績' => self::BY_CUSTOMER
    ];
}
