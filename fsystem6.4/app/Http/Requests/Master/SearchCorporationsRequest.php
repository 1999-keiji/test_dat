<?php
declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\CorporationCode;

class SearchCorporationsRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\CorporationCode
     */
    private $corporation_code;

    /**
     * @param  \App\ValueObjects\String\CorporationCode $corporation_code
     * @return void
     */
    public function __construct(CorporationCode $corporation_code)
    {
        $this->corporation_code = $corporation_code;
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
            'corporation_code' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->corporation_code->getMinLength()}",
                "max:{$this->corporation_code->getMaxLength()}",
                "regex:{$this->corporation_code->getRegexPattern()}"
            ],
            'corporation_name' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }
}
