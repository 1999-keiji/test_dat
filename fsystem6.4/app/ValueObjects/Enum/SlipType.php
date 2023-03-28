<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class SlipType extends Enum
{
    public const NORMAL_SLIP = 1;
    public const CREDIT_SLIP = 2;

    protected const ENUM = [
        '通常' => self::NORMAL_SLIP,
        '赤黒伝票' => self::CREDIT_SLIP
    ];
}
