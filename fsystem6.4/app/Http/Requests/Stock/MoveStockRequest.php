<?php

declare(strict_types=1);

namespace App\Http\Requests\Stock;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryWarehouse;

class MoveStockRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\FactoryWarehouse
     */
    private $factory_warehouse;

    /**
     * @param  \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @return void
     */
    public function __construct(FactoryWarehouse $factory_warehouse)
    {
        $this->factory_warehouse = $factory_warehouse;
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
        $stock = $this->route('stock');
        $rules = [
            'warehouse_code' => [
                'bail',
                'required',
                Rule::exists($this->factory_warehouse->getTable())->where(function ($query) use ($stock) {
                    $query->where('factory_code', $stock->factory_code);
                }),
                'not_in:'.$this->route('stock')->warehouse_code
            ],
            'moving_start_at' => ['bail', 'required', 'date_format:Y/m/d', 'after_or_equal:'.$stock->harvesting_date],
            'moving_complete_at' => ['bail', 'required', 'date_format:Y/m/d', 'after_or_equal:moving_start_at'],
            'moving_lead_time' => ['bail', 'required', 'integer', 'min:0', 'max:9'],
            'stock_quantity' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'max:'.$stock->getStockQuantityExceptDisposed()
            ]
        ];

        if (! is_null($stock->moving_complete_at)) {
            $rules['moving_start_at'][] = 'after_or_equal:'.$stock->moving_complete_at;
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
            'moving_start_at.after_or_equal' =>
                '移動開始日には、収穫日以降もしくは同日を指定してください。'.
                '移動中の在庫の場合、移動完了日以降もしくは同日を指定してください。',
        ];
    }
}
