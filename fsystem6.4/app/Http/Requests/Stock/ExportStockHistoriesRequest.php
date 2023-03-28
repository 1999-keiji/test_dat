<?php

declare(strict_types=1);

namespace App\Http\Requests\Stock;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\Models\Master\FactoryWarehouse;
use App\Models\Master\User;

class ExportStockHistoriesRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Models\Master\FactorySpecies
     */
    private $factory_species;

    /**
     * @var \App\Models\Master\FactoryWarehouse
     */
    private $factory_warehouse;

    /**
     * @var \App\Models\Master\User
     */
    private $user;

    /**
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @param  \App\Models\Master\User $user
     * @return void
     */
    public function __construct(
        Factory $factory,
        FactorySpecies $factory_species,
        FactoryWarehouse $factory_warehouse,
        User $user
    ) {
        $this->factory = $factory;
        $this->factory_species = $factory_species;
        $this->factory_warehouse = $factory_warehouse;
        $this->user = $user;
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
                'nullable',
                Rule::exists($this->factory_warehouse->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'species_code' => [
                'bail',
                'nullable',
                Rule::exists($this->factory_species->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'screen' => ['bail', 'nullable', 'in:'.implode(',', array_keys(config('constant.stock.screens')))],
            'harvesting_date_from' => ['bail', 'nullable', 'date_format:Y/m/d'],
            'harvesting_date_to' => ['bail', 'nullable', 'date_format:Y/m/d', 'after_or_equal:harvesting_date_from'],
            'user_code' => ['bail', 'nullable', "exists:{$this->user->getTable()}"],
            'working_date_from' => ['bail', 'required', 'date_format:Y/m/d'],
            'working_date_to' => ['bail', 'required', 'date_format:Y/m/d', 'after_or_equal:working_date_from'],
            'delivery_date_from' => ['bail', 'nullable', 'date_format:Y/m/d'],
            'delivery_date_to' => ['bail', 'nullable', 'date_format:Y/m/d', 'after_or_equal:delivery_date_from']
        ];
    }
}
