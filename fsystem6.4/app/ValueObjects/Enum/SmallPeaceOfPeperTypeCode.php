<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class SmallPeaceOfPeperTypeCode extends Enum
{
    public const NORMAL = '01';
    public const CHARGED_SUPPLYING = '07';
    public const IN_COMPANY_PROCESSING = '20';
    public const SAMPLE = '31';

    protected const ENUM = [
        '通常' => self::NORMAL,
        '有償支給' => self::CHARGED_SUPPLYING,
        '社内加工' => self::IN_COMPANY_PROCESSING,
        'サンプル' => self::SAMPLE
    ];
}
