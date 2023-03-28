<?php

declare(strict_types=1);

namespace App\ValueObjects\Decimal;

final class ProductSize extends PositiveDecimal
{
    /**
     * @var float
     */
    protected const MAXIMUM_NUM = 99999.99;

     /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A([1-9][0-9]{0,5}|0)(\.[0-9]{1,2})?\z/";

    /**
     * @var int
     */
    protected const DECIMALS = 2;
}
