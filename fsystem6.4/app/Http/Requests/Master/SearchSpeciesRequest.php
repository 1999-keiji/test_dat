<?php
declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\SpeciesCode;

class SearchSpeciesRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\SpeciesCode
     */
    private $species_code;

    /**
     * @param  \App\ValueObjects\String\SpeciesCode $species_code
     * @return void
     */
    public function __construct(SpeciesCode $species_code)
    {
        $this->species_code = $species_code;
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
            'species_code' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->species_code->getMinLength()}",
                "max:{$this->species_code->getMaxLength()}",
                "regex:{$this->species_code->getRegexPattern()}",
            ],
            'species_name' => ['bail', 'nullable', 'string', 'max:20']
        ];
    }
}
