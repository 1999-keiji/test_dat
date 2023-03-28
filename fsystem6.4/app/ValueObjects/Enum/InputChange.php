<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class InputChange extends Enum
{
    public const HARVESTING = 1;
    public const SEEDING = 2;

    protected const ENUM = [
        '収穫基準' => self::HARVESTING,
        '播種基準' => self::SEEDING
    ];
}
