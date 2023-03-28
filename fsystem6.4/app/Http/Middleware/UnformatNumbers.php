<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class UnformatNumbers extends TransformsRequest
{
    /**
     * The attributes that should be unformatted.
     *
     * @var array
     */
    protected $unformat = [
        'sales_order_unit_quantity',
        'minimum_sales_order_unit_quantity',
        'net_weight',
        'gross_weight',
        'depth',
        'width',
        'height',
        'unit_price',
        'order_unit',
        'order_amount',
        'received_order_unit',
        'customer_received_order_unit',
        'stock_quantity',
        'disposal_quantity'
    ];

    /**
     * @return void
     */
    public function __construct()
    {
        foreach (range(1, config('settings.number_of_reserved_item')) as $idx) {
            $this->unformat[] = "reserved_number{$idx}";
        }
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (! in_array($key, $this->unformat, true) && ! is_int($key)) {
            return $value;
        }

        return is_string($value) ? str_replace([','], '', $value) : $value;
    }
}
