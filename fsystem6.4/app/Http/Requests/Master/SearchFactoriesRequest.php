<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\Models\Master\Corporation;
use App\ValueObjects\String\FactoryCode;

class SearchFactoriesRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\CorporationCode
     */
    private $corporation;

    /**
     * @var \App\ValueObjects\String\ProductCode
     */
    private $factory_code;

    /**
     * @param  \App\ValueObjects\String\CorporationCode $corporation_code
     * @param  \App\ValueObjects\String\FactoryCode $factory_code
     * @return void
     */
    public function __construct(Corporation $corporation, FactoryCode $factory_code)
    {
        $this->corporation  = $corporation;
        $this->factory_code = $factory_code;
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
            'corporation_code' => ['bail', 'nullable', "exists:{$this->corporation->getTable()}"],
            'factory_code' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->factory_code->getMinLength()}",
                "max:{$this->factory_code->getMaxLength()}",
                "regex:{$this->factory_code->getRegexPattern()}"
            ],
            'factory_name' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }
}
