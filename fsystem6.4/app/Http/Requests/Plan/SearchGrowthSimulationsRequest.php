<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;

class SearchGrowthSimulationsRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Model\Master\FactorySpecies
     */
    private $factory_species;

    /**
     * @param  \App\Model\Master\Species $species
     * @param  \App\Model\Master\Factory $factory
     * @return void
     */
    public function __construct(
        Factory $factory,
        FactorySpecies $factory_species
    ) {
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
            'factory_code' => ['bail', 'nullable', "exists:{$this->factory->getTable()}"],
            'factory_species_code' => [
                'bail',
                'nullable',
                Rule::exists($this->factory_species->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'simulation_name' => ['bail', 'nullable', 'string', 'max:50'],
            'user_name' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }
}
