<?php

namespace App\Http\Requests\Plan;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\ValueObjects\Date\WorkingDate;

class CreateBedStateRequest extends FormRequest
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
     * @var \App\ValueObjects\Date\WorkingDate $working_date
     */
    private $working_date;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\ValueObjects\Date\WorkingDate $factory_species
     * @return void
     */
    public function __construct(Factory $factory, FactorySpecies $factory_species, WorkingDate $working_date)
    {
        $this->factory = $factory;
        $this->factory_species = $factory_species;
        $this->working_date = $working_date;
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
                'required',
                Rule::exists($this->factory_species->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'start_of_week' => ['bail', 'required', "date_format:{$this->working_date->getDateFormat()}"]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'start_of_week' => trans('view.global.date')
        ];
    }
}
