<?php

namespace App\Http\Requests\Plan;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;

class ExportGrowthPlannedTableRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory $factory
     */
    private $factory;

    /**
     * @var \App\Models\Master\FactorySpecies $factory_species
     */
    private $factory_species;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return void
     */
    public function __construct(Factory $factory, FactorySpecies $factory_species)
    {
        $this->factory = $factory;
        $this->factory_species = $factory_species;
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
            'factory_species_code' => [
                'bail',
                'nullable',
                Rule::exists($this->factory_species->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'date_from' => ['bail', 'required', 'date'],
            'date_range' => ['bail', 'required', Rule::in([4, 8, 12])]
        ];
    }
}
