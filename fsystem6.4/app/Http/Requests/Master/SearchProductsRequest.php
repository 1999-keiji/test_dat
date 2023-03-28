<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\Models\Master\Species;
use App\ValueObjects\String\ProductCode;

class SearchProductsRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\Species
     */
    private $species;

    /**
     * @var \App\ValueObjects\String\ProductCode
     */
    private $product_code;

    /**
     * @param  \App\Model\Master\Species $species
     * @param  \App\ValueObjects\String\ProductCode $product_code
     * @return void
     */
    public function __construct(Species $species, ProductCode $product_code)
    {
        $this->species = $species;
        $this->product_code = $product_code;
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
            'species_code' => ['bail', 'nullable', "exists:{$this->species->getTable()}"],
            'product_code' => [
                'bail',
                'nullable',
                'string',
                "max:{$this->product_code->getMaxLength()}",
                "regex:{$this->product_code->getRegexPattern()}"
            ],
            'product_name' => ['bail', 'nullable', 'string', 'max:40']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'species_code' => trans('view.master.species.species')
        ];
    }
}
