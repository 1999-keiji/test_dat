<?php

declare(strict_types=1);

namespace App\ValueObjects\Decimal;

use JsonSerializable;

final class UnitPrice extends PositiveDecimal implements JsonSerializable
{
    /**
     * @return float
     */
    public function jsonSerialize(): float
    {
        return $this->value();
    }
}
