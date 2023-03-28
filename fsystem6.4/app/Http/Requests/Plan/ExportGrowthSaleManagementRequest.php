<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\Species;

class ExportGrowthSaleManagementRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Models\Master\Species
     */
    private $species;

    /**
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @return void
     */
    public function __construct(Factory $factory, Species $species)
    {
        $this->factory = $factory;
        $this->species = $species;
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
            'species_code' => ['bail', 'required', "exists:{$this->species->getTable()}"],
            'harvesting_date' => ['bail', 'required', 'date', 'max:10']
        ];
    }
}
