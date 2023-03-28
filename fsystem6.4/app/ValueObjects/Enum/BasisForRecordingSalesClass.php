<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class BasisForRecordingSalesClass extends Enum
{
    public const ORDER = '1';
    public const ACCEPTANCE = '2';
    public const DEEMED_ARRIVAL = '4';

    protected const ENUM = [
        '受注基準' => self::ORDER,
        '検収基準' => self::ACCEPTANCE,
        'みなし着荷基準' => self::DEEMED_ARRIVAL
    ];
}
