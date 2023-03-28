<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\CustomerCode;
use App\ValueObjects\String\EndUserCode;

class SearchEndUsersRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\CustomerCode
     */
    private $customer_code;

    /**
     * @var \App\ValueObjects\String\EndUserCode
     */
    private $end_user_code;

    /**
     * @param  \App\ValueObjects\String\CustomerCode $customer_code
     * @param  \App\ValueObjects\String\EndUserCode $end_user_code
     * @return void
     */
    public function __construct(CustomerCode $customer_code, EndUserCode $end_user_code)
    {
        $this->customer_code = $customer_code;
        $this->end_user_code = $end_user_code;
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
            'customer_code' => [
                'bail',
                'nullable',
                'string',
                "max:{$this->customer_code->getMaxLength()}",
                "regex:{$this->customer_code->getRegexPattern()}"
            ],
            'customer_name' => ['bail', 'nullable', 'string', 'max:50'],
            'end_user_code' => [
                'bail',
                'nullable',
                'string',
                "max:{$this->end_user_code->getMaxLength()}",
                "regex:{$this->end_user_code->getRegexPattern()}"
            ],
            'end_user_name' => ['bail', 'nullable', 'string', 'max:50'],
            'past_flag' => ['bail', 'required', 'boolean']
        ];
    }
}
