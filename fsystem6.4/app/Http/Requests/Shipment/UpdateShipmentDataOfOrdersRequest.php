<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\CollectionTime;
use App\Models\Master\TransportCompany;
use App\Models\Order\Order;

class UpdateShipmentDataOfOrdersRequest extends FormRequest
{
    /**
     * @var \App\Models\Order\Order
     */
    private $order;

    /**
     * @var \App\Models\Master\TransportCompany
     */
    private $transport_company;

    /**
     * @var \App\Models\Master\CollectionTime
     */
    private $collection_time;

    /**
     * @param  \App\Models\Order\Order $order
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @return void
     */
    public function __construct(Order $order, TransportCompany $transport_company, CollectionTime $collection_time)
    {
        $this->order = $order;
        $this->transport_company = $transport_company;
        $this->collection_time = $collection_time;
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
        $rules = [
            'orders' => ['bail', 'required', 'array'],
            'orders.*.order_number' => [
                'bail',
                'required',
                Rule::exists($this->order->getTable(), 'order_number')->where(function ($query) {
                    $query->whereNull('fixed_shipping_at');
                })
            ],
            'orders.*.shipping_date' => [
                'bail',
                'required',
                'date',
                'before_or_equal:orders.*.delivery_date'
            ],
            'orders.*.transport_company_code' => ['bail', 'required', "exists:{$this->transport_company->getTable()}"]
        ];

        foreach ($this->orders as $idx => $o) {
            $rules["orders.{$idx}.collection_time_sequence_number"] = [
                'bail',
                'required',
                Rule::exists($this->collection_time->getTable(), 'sequence_number')->where(function ($query) use ($o) {
                    $query->where('transport_company_code', $o['transport_company_code'] ?? null);
                })
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'orders.*.order_number.exists' => '未出荷状態の注文を選択してください。',
            'orders.*.shipping_date.required' => '出荷日は、必ず指定してください。',
            'orders.*.shipping_date.date' => '出荷日は、正しい日付ではありません。',
            'orders.*.shipping_date.before_or_equal' => '出荷日は、納入日よりも前の日付を入力してください。',
            'orders.*.transport_company_code.required' => '運送会社は、必ず指定してください。',
            'orders.*.transport_company_code.exists' => '選択された運送会社は、有効ではありません。',
            'orders.*.collection_time_sequence_number.required' => '集荷時間は、必ず指定してください。',
            'orders.*.collection_time_sequence_number.exists' => '選択された集荷時間は、有効ではありません。'
        ];
    }
}
