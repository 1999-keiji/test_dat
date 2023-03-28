<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ExportExchangeRateCode extends Enum
{
    public const FOB = '01';
    public const CIF = '02';
    public const C_F = '03';
    public const CIP = '04';
    public const CPT = '05';
    public const DAT = '06';
    public const DAP = '07';
    public const DDP = '08';
    public const DDU = '09';
    public const EXW = '10';
    public const FAS = '11';
    public const FCA = '12';
    public const FH = '13';
    public const OTHERS = '14';

    protected const ENUM = [
        'FOB' => self::FOB,
        'CIF' => self::CIF,
        'C/F' => self::C_F,
        'CIP' => self::CIP,
        'CPT' => self::CPT,
        'DAT' => self::DAT,
        'DAP' => self::DAP,
        'DDP' => self::DDP,
        'DDU' => self::DDU,
        'EXW' => self::EXW,
        'FAS' => self::FAS,
        'FCA' => self::FCA,
        'FH' => self::FH,
        'OTHERS' => self::OTHERS
    ];
}
