<?php

declare(strict_types=1);

namespace App\ValueObjects\Integer;

final class GrowingTerm extends PositiveInteger
{
    /**
     * @var int
     */
    protected const MINIMUM_NUM = 1;

    /**
     * @var int
     */
    protected const MAXIMUM_NUM = 28;
}
