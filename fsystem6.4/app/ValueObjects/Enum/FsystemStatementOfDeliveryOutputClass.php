<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class FsystemStatementOfDeliveryOutputClass extends Enum
{
    public const DISABLED = 0;
    public const DISPLAY_PRICE = 1;
    public const NOT_DISPLAY_PRICE = 2;

    protected const ENUM = [
        '納品書なし' => self::DISABLED,
        '価格あり納品書' => self::DISPLAY_PRICE,
        '価格なし納品書' => self::NOT_DISPLAY_PRICE,
    ];

    /**
     * デフォルトの値を取得
     *
     * @return int
     */
    public function getDefaultValue(): int
    {
        return self::NOT_DISPLAY_PRICE;
    }
}
