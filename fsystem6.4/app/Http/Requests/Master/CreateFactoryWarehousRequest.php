<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryWarehouse;
use App\Models\Master\Warehouse;

class CreateFactoryWarehousRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Warehouse
     */
    private $warehouse;

    /**
     * @var \App\Models\Master\FactoryWarehouse
     */
    private $factory_warehouse;

    /**
     * @param \App\Models\Master\Warehouse $warehouse
     * @param \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @return void
     */
    public function __construct(Warehouse $warehouse, FactoryWarehouse $factory_warehouse)
    {
        $this->warehouse = $warehouse;
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
            'warehouse_code' => [
                'bail',
                'required',
                "exists:{$this->warehouse->getTable()}",
                Rule::unique($this->factory_warehouse->getTable())->where(function ($query) {
                    return $query->where('factory_code', $this->route('factory')->factory_code);
                })
            ]
        ];
    }
}
