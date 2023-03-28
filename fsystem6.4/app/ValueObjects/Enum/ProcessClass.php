<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ProcessClass extends Enum
{
    public const NEW_PROCESS = '1';
    public const CHANGE_PROCESS = '2';
    public const CANCEL_PROCESS = '9';

    protected const ENUM = [
        '新規' => self::NEW_PROCESS,
        '変更' => self::CHANGE_PROCESS,
        '取消' => self::CANCEL_PROCESS
    ];
}
