<?php

declare(strict_types=1);

namespace App\Http\Requests\Stock;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactoryWarehouse;

class SearchStocktakingRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Models\Master\FactoryWarehouse
     */
    private $factory_warehouse;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @return void
     */
    public function __construct(Factory $factory, FactoryWarehouse $factory_warehouse)
    {
        $this->factory = $factory;
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
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'warehouse_code' => [
                'bail',
                'required',
                Rule::exists($this->factory_warehouse->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'stocktaking_month' => ['bail', 'required', 'date_format:Y/m']
        ];
    }
}
