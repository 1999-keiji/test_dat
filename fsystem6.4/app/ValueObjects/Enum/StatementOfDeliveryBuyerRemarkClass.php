<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class StatementOfDeliveryBuyerRemarkClass extends Enum
{
    public const INDIVIDUAL_USE = '1';
    public const NEW_PARTIAL = '2';

    protected const ENUM = [
        '各社個別使用項目' => self::INDIVIDUAL_USE,
        '新規/分納' => self::NEW_PARTIAL
    ];
}
