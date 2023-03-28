<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class DisposalStatus extends Enum
{
    public const STOCK = 1;
    public const PART_DISPOSAL = 2;
    public const DISPOSAL  = 0;

    protected const ENUM = [
        '在庫' => self::STOCK,
        '一部廃棄' => self::PART_DISPOSAL,
        '廃棄' => self::DISPOSAL
    ];

    /**
     * 廃棄を除く廃棄状態を取得
     *
     * @return array
     */
    public static function getDisposalStatusExceptDisposal(): array
    {
        return array_filter(self::ENUM, function ($value) {
            return $value !== self::DISPOSAL;
        });
    }

    /**
     * 一部廃棄を除く廃棄状態を取得
     *
     * @return array
     */
    public static function getDisposalStatusExceptPartDisposal(): array
    {
        return array_filter(self::ENUM, function ($value) {
            return $value !== self::PART_DISPOSAL;
        });
    }
}
