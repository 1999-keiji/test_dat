<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryProduct;
use App\Models\Order\ReturnedProduct;
use App\ValueObjects\Decimal\OrderUnit;

class CreateReturnedProductRequest extends FormRequest
{
    /**
     * @var \App\Models\Order\ReturnedProduct
     */
    private $returned_product;

    /**
     * @var \App\Models\Master\FactoryProduct
     */
    private $factory_product;

    /**
     * @var \App\ValueObjects\Decimal\OrderUnit
     */
    private $order_unit;

    /**
     * @param  \App\Models\Order\ReturnedProduct $returned_product
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  \App\ValueObjects\Decimal\OrderUnit $order_unit
     * @return void
     */
    public function __construct(
        ReturnedProduct $returned_product,
        FactoryProduct $factory_product,
        OrderUnit $order_unit
    ) {
        $this->returned_product = $returned_product;
        $this->factory_product =$factory_product;
        $this->order_unit = $order_unit;
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
            'order_number' => [
                'bail',
                'required',
                Rule::unique($this->returned_product->getTable())
            ],
            'returned_on' => ['bail', 'required', 'date_format:Y/m/d'],
            'factory_product_sequence_number' => [
                'bail',
                'required',
                Rule::exists($this->factory_product->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('factory_code', $this->route('order')->factory_code);
                })
            ],
            'unit_price' => [
                'bail',
                'required',
                "min:{$this->order_unit->getMinimumNum()}",
                "max:{$this->order_unit->getMaximumNum()}",
                "regex:{$this->order_unit->getRegexPattern()}"
            ],
            'quantity' => ['bail', 'required', 'integer', 'min:0', 'max:99999'],
            'remark' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'factory_product_sequence_number' => trans('view.order.return_input.return_product'),
            'unit_price' => trans('view.order.return_input.return_unit_price'),
            'quantity' => trans('view.order.return_input.returned_product_quantity'),
        ];
    }
}
