<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Order\Order;

class FixShippingOrderedProductsRequest extends FormRequest
{
    /**
     * @var \App\Models\Order\Order
     */
    private $order;

    /**
     * @param  \App\Models\Order\Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
            'order_numbers' => ['bail', 'required', 'array'],
            'order_numbers.*' => [
                'bail',
                'required',
                Rule::exists($this->order->getTable(), 'order_number')->where(function ($query) {
                    $query->whereNull('fixed_shipping_at');
                })
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_numbers.required' => '出荷確定対象の注文を選択してください。',
            'order_numbers.*.exists' => '未出荷状態の注文を選択してください。'
        ];
    }
}
