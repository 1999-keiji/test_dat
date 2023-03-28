<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class SmallPeaceOfPeperTypeClass extends Enum
{
    public const ORDER = 'C';

    protected const ENUM = [
        '発注' => self::ORDER
    ];
}
