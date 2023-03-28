<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class CurrencyCode extends MasterCode
{
    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[A-Z]+\z/";

    /**
     * @var int
     */
    protected const MAX_LENGTH = 3;

    /**
     * @var string
     */
    private const DEFAULT_CURRENCY_CODE = 'JPY';

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("%d文字の半角英大文字が入力できます。", $this->getMaxLength());
    }

    /**
     * @return string
     */
    public static function getDefaultCurrencyCode(): string
    {
        return self::DEFAULT_CURRENCY_CODE;
    }
}
