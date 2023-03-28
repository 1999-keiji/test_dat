<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ExportManagingClass extends Enum
{
    public const APPROVED = '1';
    public const NOT_APPROVED = '2';

    protected const ENUM = [
        '輸出承認済' => self::APPROVED,
        '輸出未承認' => self::NOT_APPROVED
    ];
}
