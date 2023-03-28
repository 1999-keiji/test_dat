<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryWarehouse;

class SaveProductizedResultRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\FactoryWarehouse;
     */
    private $factory_warehouse;

    /**
     *
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
        return [
            'productized_result.triming' => ['bail', 'required', 'integer', 'min:0', 'digits_between:1,9'],
            'productized_result.product_failure' => ['bail', 'required', 'integer', 'min:0', 'digits_between:1,9'],
            'productized_result.packing' => ['bail', 'required', 'integer', 'min:0', 'digits_between:1,9'],
            'productized_result.crop_failure' => ['bail', 'required', 'integer', 'max:0'],
            'productized_result.sample' => ['bail', 'required', 'integer', 'min:0', 'digits_between:1,9'],
            'productized_result.advanced_harvest' => ['bail', 'required', 'integer', 'min:-99999', 'max:99999'],
            'productized_result.weight_of_discarded' => ['bail', 'required', 'numeric', 'min:0'],
            'productized_result_details' => ['bail', 'nullable', 'array'],
            'productized_result_details.*.product_quantity' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'digits_between:1,9'
            ]
        ];
    }
}
