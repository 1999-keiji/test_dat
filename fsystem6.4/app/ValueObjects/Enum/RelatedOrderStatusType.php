<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class RelatedOrderStatusType extends Enum
{
    public const UN_RELATED     = 0;
    public const AUTO_RELATED   = 1;
    public const MANUAL_RELATED = 2;

    protected const ENUM = [
        '未紐付' => self::UN_RELATED,
        '自動紐付' => self::AUTO_RELATED,
        '手動紐付' => self::MANUAL_RELATED
    ];
}
