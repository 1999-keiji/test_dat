<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ProductClass extends Enum
{
    public const PRODUCT = '1';
    public const NOT_PRODUCT = '2';

    protected const ENUM = [
        '製品' => self::PRODUCT,
        '製品外' => self::NOT_PRODUCT
    ];
}
