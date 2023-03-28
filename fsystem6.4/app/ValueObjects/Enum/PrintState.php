<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PrintState extends Enum
{
    public const UNPRINTED = 0;
    public const PRINTED = 1;
    public const ALL = 2;

    protected const ENUM = [
        '未印字' => self::UNPRINTED,
        '印字済' => self::PRINTED,
        'すべて' => self::ALL
    ];
}
