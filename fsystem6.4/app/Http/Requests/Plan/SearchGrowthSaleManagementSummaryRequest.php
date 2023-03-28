<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use App\Http\Requests\FormRequest;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\Species;

class SearchGrowthSaleManagementSummaryRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'plan.growth_sale_management_summary.index';

    /**
     * @var \App\Model\Master\Species
     */
    private $species;

    /**
     * @var \App\Model\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Model\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @param  \App\Model\Master\Species $species
     * @param  \App\Model\Master\Factory $factory
     * @param  \App\Model\Master\DeliveryDestination $delivery_destination
     * @return void
     */
    public function __construct(
        Species $species,
        Factory $factory,
        DeliveryDestination $delivery_destination
    ) {
        $this->species = $species;
        $this->factory = $factory;
        $this->delivery_destination = $delivery_destination;
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
            'display_type' => ['bail', 'required', 'in:factories,factory_species,delivery_destination'],
            'display_term' => ['bail', 'required', 'in:date,month'],
            'display_from_date' => ['bail', 'required_if:display_term,date', 'date_format:Y/m/d'],
            'week_term' => ['bail', 'required_if:display_term,date', 'in:1,2,3'],
            'display_from_month' => ['bail', 'required_if:display_term,month', 'date_format:Y/m'],
            'display_unit' => ['bail', 'required', 'in:weight,quantity'],
            'species_code' => ['bail', 'required_with:display_type', "exists:{$this->species->getTable()}"],
            'factory_code' => [
                'bail',
                'required_if:display_type,factory_species',
                "exists:{$this->factory->getTable()}"
            ],
            'delivery_destination_code' => [
                'bail',
                'required_if:display_type,delivery_destination',
                "exists:{$this->delivery_destination->getTable()}"
            ]
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
            'display_from_month.date_format' => '表示期間が月単位の場合、年月を選択してください。',
            'species_code.required_with' => '品種を選択してください。',
            'factory_code.required_if' => '表示切替が工場-品種単位の場合、工場を選択してください。',
            'delivery_destination_code.required_if' => '表示切替が納入先単位の場合、納入先を選択してください。',
        ];
    }
}
