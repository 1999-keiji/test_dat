<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class StatementOfDeliveryRemarkClass extends Enum
{
    public const CLIENT_REMARK = '1';
    public const PRODUCT_NAME = '2';
    public const LABEL_COMMENT = '3';
    public const PUBLISHED_DATE = '4';

    protected const ENUM = [
        '受注者用備考' => self::CLIENT_REMARK,
        '納品書品名(品名)' => self::PRODUCT_NAME,
        '納品書ラベルコメント' => self::LABEL_COMMENT,
        '納品書発行日' => self::PUBLISHED_DATE
    ];
}
