<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;

class SaveProductAllocationsRequest extends FormRequest
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
        return [
            'warehouse_code' => [
                'bail',
                'required',
                Rule::in($this->route('factory')->getAllocatableWarehouses()->pluck('warehouse_code')->all())
            ],
            'factory_product_sequence_numbers' => ['bail', 'required', 'array'],
            'factory_products' => ['bail', 'nullable', 'array']
        ];
    }
}
