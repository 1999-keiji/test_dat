<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PanelStatus extends Enum
{
    public const EMPTY = 0;
    public const OPERATION = 1;

    protected const ENUM = [
        '空'   => self::EMPTY,
        '稼働' => self::OPERATION
    ];
}
