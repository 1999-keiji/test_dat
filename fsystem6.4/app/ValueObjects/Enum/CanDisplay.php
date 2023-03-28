<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class CanDisplay extends Enum
{
    public const CAN_DISPLAY = 1;
    public const CAN_NOT_DISPLAY = 0;

    protected const ENUM = [
        '表示' => self::CAN_DISPLAY,
        '非表示' => self::CAN_NOT_DISPLAY
    ];

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return "生産販売管理表に表示される\n納入先の表示・非表示を制御";
    }
}
