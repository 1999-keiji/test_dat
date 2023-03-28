<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class AllocationStatus extends Enum
{
    public const UNALLOCATED = 0;
    public const ALLOCATED = 1;
    public const PART_ALLOCATED = 2;

    protected const ENUM = [
        '未引当' => self::UNALLOCATED,
        '引当済' => self::ALLOCATED,
        '部分引当' => self::PART_ALLOCATED
    ];

    /**
     * 部分引当を除く引当状態を取得
     *
     * @return array
     */
    public static function getAllocationStatusExceptPartAllocated(): array
    {
        return array_filter(self::ENUM, function ($value) {
            return $value !== self::PART_ALLOCATED;
        });
    }
}
