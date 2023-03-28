<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;

class AddSearchGrowthSimulationRequest extends FormRequest
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
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'factory_species_code' => [
                'bail',
                'required',
                Rule::exists($this->factory_species->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'display_term' => ['bail', 'required', 'in:date,month'],
            'display_from_date' => ['bail', 'required_if:display_term,date', 'date_format:Y/m/d'],
            'week_term' => ['bail', 'required_if:display_term,date', 'in:1,2,3'],
            'display_from_month' => ['bail', 'required_if:display_term,month', 'date_format:Y/m'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'display_from_date.required_if' => '表示期間が日単位の場合、日付を選択してください。',
            'display_from_date.date_format' => '表示期間が日単位の場合、日付を選択してください。',
            'display_from_month.required_if' => '表示期間が月単位の場合、年月を選択してください。',
            'display_from_month.date_format' => '表示期間が月単位の場合、年月を選択してください。'
        ];
    }
}
