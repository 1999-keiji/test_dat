<?php

declare(strict_types=1);

namespace App\ValueObjects\Decimal;

final class ProductWeight extends PositiveDecimal
{
    /**
     * @var float
     */
    protected const MAXIMUM_NUM = 999999.999;

     /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A([1-9][0-9]{0,6}|0)(\.[0-9]{1,3})?\z/";

    /**
     * @var int
     */
    protected const DECIMALS = 3;
}
