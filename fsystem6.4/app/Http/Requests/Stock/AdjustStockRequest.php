<?php

declare(strict_types=1);

namespace App\Http\Requests\Stock;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\ValueObjects\Enum\StockStatus;

class AdjustStockRequest extends FormRequest
{
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
            'adjusting_type' => ['bail', 'required', 'in:replace,separate,change'],
            'number_of_heads' => ['bail', 'required_if:adjusting_type,replace'],
            'weight_per_number_of_heads' => ['bail', 'required_if:adjusting_type,replace'],
            'input_group' => ['bail', 'required_if:adjusting_type,replace'],
            'stock_quantity' => ['bail', 'required_unless:adjusting_type,change', 'integer', 'min:1'],
            'stock_status' => [
                'bail',
                'required_if:adjusting_type,change',
                'in:'.implode(',', (new StockStatus())->all())
            ]
        ];

        if ($this->adjusting_type === 'replace') {
            $rules['stock_quantity'][] = 'max:999999';
        }
        if ($this->adjusting_type === 'separate') {
            $rules['stock_quantity'][] = 'max:'.($this->route('stock')->getStockQuantityExceptDisposed() - 1);
        }

        return $rules;
    }
}
