<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

use App\Models\Master\Factory;

class ShippingDate extends Date
{
    /**
     * 帳票用出荷日を取得する
     *
     * @param  \App\Models\Master\Factory $factory
     * @return \App\ValueObjects\Date\ShippingDate
     */
    public function getPrintingShippingDate(Factory $factory): ShippingDate
    {
        $shipping_date = $this;
        if (! $factory->needs_to_slide_printing_shipping_date) {
            return $shipping_date;
        }

        while (true) {
            if ($shipping_date->isWorkingDay($factory)) {
                break;
            }

            $shipping_date = $shipping_date->subDay();
        }

        return $shipping_date;
    }

    /**
     * Prepare the object for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'date' => $this->value(),
            'date_except_year' => $this->format('n/j'),
            'day_of_the_week_ja' => $this->dayOfWeekJa()
        ];
    }
}
