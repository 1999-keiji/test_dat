<?php
declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Currency;
use App\Models\Master\DeliveryFactoryProduct;
use App\ValueObjects\Decimal\OrderAmount;
use App\ValueObjects\Decimal\OrderUnit;

class CreateOrderManuallyRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\DeliveryFactoryProduct
     */
    private $delivery_factory_product;

    /**
     * @var \App\Models\Master\Currency
     */
    private $currency;

    /**
     * @var \App\ValueObjects\Decimal\OrderUnit
     */
    private $order_unit;

    /**
     * @var \App\ValueObjects\Decimal\OrderAmount
     */
    private $order_amount;

    /**
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @param  \App\Models\Master\Currency $currency
     * @param  \App\ValueObjects\Decimal\OrderUnit $order_unit
     * @param  \App\ValueObjects\Decimal\OrderAmount $order_amount
     * @return void
     */
    public function __construct(
        DeliveryFactoryProduct $delivery_factory_product,
        Currency $currency,
        OrderUnit $order_unit,
        OrderAmount $order_amount
    ) {
        $this->delivery_factory_product = $delivery_factory_product;
        $this->currency = $currency;
        $this->order_unit = $order_unit;
        $this->order_amount = $order_amount;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'delivery_date' => ['bail', 'required', 'date_format:Y/m/d'],
            'factory_product_sequence_number' => [
                'bail',
                'required',
                Rule::exists($this->delivery_factory_product->getTable())->where(function ($query) {
                    $query->where('delivery_destination_code', $this->delivery_destination_code)
                        ->where('factory_code', $this->factory_code);
                })
            ],
            'currency_code'  => ['bail', 'required', "exists:{$this->currency->getTable()}"],
            'order_quantity' => ['bail', 'required', 'integer', 'min:0', 'max:99999'],
            'order_unit' => [
                'bail',
                'required',
                "min:{$this->order_unit->getMinimumNum()}",
                "max:{$this->order_unit->getMaximumNum()}",
                "regex:{$this->order_unit->getRegexPattern()}"
            ],
            'order_amount' => [
                'bail',
                'required',
                "min:{$this->order_amount->getMinimumNum()}",
                "max:{$this->order_amount->getMaximumNum()}",
                "regex:{$this->order_amount->getRegexPattern()}"
            ],
            'received_order_unit' => [
                'bail',
                'nullable',
                "min:{$this->order_unit->getMinimumNum()}",
                "max:{$this->order_unit->getMaximumNum()}",
                "regex:{$this->order_unit->getRegexPattern()}"
            ],
            'customer_received_order_unit' => [
                'bail',
                'nullable',
                "min:{$this->order_amount->getMinimumNum()}",
                "max:{$this->order_amount->getMaximumNum()}",
                "regex:{$this->order_amount->getRegexPattern()}"
            ],
            'base_plus_order_number' => ['bail', 'nullable', 'string', 'max:10'],
            'base_plus_order_chapter_number' => ['bail', 'nullable', 'string', 'max:3'],
            'end_user_order_number' => ['bail', 'nullable', 'string', 'max:17'],
            'order_message' => ['bail', 'nullable', 'string', 'max:50'],
        ];
    }
}
