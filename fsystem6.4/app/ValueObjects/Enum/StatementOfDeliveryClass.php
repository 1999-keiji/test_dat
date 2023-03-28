<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class StatementOfDeliveryClass extends Enum
{
    public const NOT_USED = '0';
    public const BASE_PLUS_NORMAL = '1';
    public const EIAJ_NORMAL = '2';
    public const END_USER_PUBLISHED = '3';
    public const END_USER_COLLATED = '4';
    public const END_USER_WEB = '5';

    protected const ENUM = [
        '(未使用)' => self::NOT_USED,
        'BASE+標準納品書' => self::BASE_PLUS_NORMAL,
        'EIAJ標準納品書' => self::EIAJ_NORMAL,
        'エンドユーザ指定納品書発行' => self::END_USER_PUBLISHED,
        'エンドユーザ指定納品書照合' => self::END_USER_COLLATED,
        'エンドユーザ指定納品書Web' => self::END_USER_WEB
    ];
}
