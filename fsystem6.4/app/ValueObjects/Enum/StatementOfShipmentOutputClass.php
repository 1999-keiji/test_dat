<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class StatementOfShipmentOutputClass extends Enum
{
    public const DISABLED = 0;
    public const ENABLED = 1;

    protected const ENUM = [
        '出力しない' => self::DISABLED,
        '出力する' => self::ENABLED
    ];

    /**
     * デフォルトの値を取得
     *
     * @return int
     */
    public function getDefaultValue(): int
    {
        return self::ENABLED;
    }
}
