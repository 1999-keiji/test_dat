<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Species;
use App\ValueObjects\Enum\OutputCondition;

class SearchWhiteboardReferenceRequest extends FormRequest
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
     * @var \App\Models\Master\FactoryProduct
     */
    private $factory_product;

    /**
     * @var \App\ValueObjects\Enum\OutputCondition
     */
    private $output_condition;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  \App\ValueObjects\Enum\OutputCondition $output_condition
     * @return void
     */
    public function __construct(
        Factory $factory,
        Species $species,
        FactoryProduct $factory_product,
        OutputCondition $output_condition
    ) {
        $this->factory = $factory;
        $this->species = $species;
        $this->factory_product = $factory_product;
        $this->output_condition = $output_condition;
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
            'year_month' => ['bail', 'required', 'date_format:Y/m'],
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'species_code' => ['bail', 'required', "exists:{$this->species->getTable()}"],
            'factory_product_sequence_number' => [
                'bail',
                'nullable',
                Rule::exists($this->factory_product->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'output_date' => ['bail', 'required', 'in:shipping_date,delivery_date'],
            'output_condition' => ['bail', 'required', Rule::in($this->output_condition->all())]
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
            'year_month' => trans('view.global.year_month')
        ];
    }
}
