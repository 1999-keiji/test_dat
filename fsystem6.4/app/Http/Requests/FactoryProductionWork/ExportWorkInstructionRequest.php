<?php

declare(strict_types=1);

namespace App\Http\Requests\FactoryProductionWork;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\ValueObjects\Date\WorkingDate;

class ExportWorkInstructionRequest extends FormRequest
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
     * @var \App\ValueObjects\Date\WorkingDate
     */
    private $working_date;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\ValueObjects\Date\WorkingDate
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
            'species_code' => [
                'bail',
                'required',
                Rule::exists($this->factory_species->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'working_date_from' => [
                'bail',
                'required',
                "date_format:{$this->working_date->getDateFormat()}"
            ],
            'working_date_to' => [
                'bail',
                'required',
                "date_format:{$this->working_date->getDateFormat()}",
                'after_or_equal:working_date_from'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'species_code.exists' => '選択された工場では、選択された品種の工場取扱品種が未登録です。'
        ];
    }
}
