<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\Species;

class SearchProductizedResultsRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Model\Master\Species
     */
    private $species;

    /**
     * @param  \App\Model\Master\Factory $factory
     * @param  \App\Model\Master\Species $species
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
            'species_code'    => ['bail', 'nullable', "exists:{$this->species->getTable()}"],
            'harvesting_date' => ['bail', 'nullable', 'date']
        ];
    }
}
