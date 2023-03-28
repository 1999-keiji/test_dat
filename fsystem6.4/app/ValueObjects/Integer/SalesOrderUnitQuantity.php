<?php

declare(strict_types=1);

namespace App\ValueObjects\Integer;

final class SalesOrderUnitQuantity extends PositiveInteger
{
    /**
     * @var int
     */
    protected const MAXIMUM_NUM = 999999999;
}
