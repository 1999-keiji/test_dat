<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class StockStatus extends Enum
{
    public const NORMAL = 1;
    public const DEFECTIVE  = 0;

    protected const ENUM = [
        '良品' => self::NORMAL,
        '不良品' => self::DEFECTIVE
    ];

    /**
     * 調整用の数量を取得
     *
     * @return int
     */
    public function getAdjustmentQuantity(int $stock_quantity): int
    {
        if ($this->value() === self::NORMAL) {
            return $stock_quantity;
        }

        return $stock_quantity * -1;
    }
}
