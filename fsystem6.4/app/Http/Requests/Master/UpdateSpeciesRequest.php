<?php
declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\CategoryCode;

class UpdateSpeciesRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\CategoryCode
     */
    private $category_code;

    /**
     * @param  \App\ValueObjects\String\CategoryCode $category_code
     * @return void
     */
    public function __construct(CategoryCode $category_code)
    {
        $this->category_code = $category_code;
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
            'species_name' => ['bail', 'required', 'string', 'max:20'],
            'species_abbreviation' => ['bail', 'required', 'string', 'max:10'],
            'remark' => ['bail', 'nullable', 'string', 'max:50'],
            'species_converters' => ['bail', 'required', 'array', 'min:1'],
            'species_converters.*.product_large_category' => [
                'bail',
                'required',
                'string',
                "min:{$this->category_code->getMinLength()}",
                "max:{$this->category_code->getMaxLength()}",
                "regex:{$this->category_code->getRegexPattern()}"
            ],
            'species_converters.*.product_middle_category' => [
                'bail',
                'required',
                'string',
                "min:{$this->category_code->getMinLength()}",
                "max:{$this->category_code->getMaxLength()}",
                "regex:{$this->category_code->getRegexPattern()}"
            ]
        ];
    }
}
