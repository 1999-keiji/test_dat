<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\CustomerCode;

class SearchCustomersRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\CustomerCode
     */
    private $customer_code;

    /**
     * @param  \App\ValueObjects\String\CustomerCode $customer_code
     * @return void
     */
    public function __construct(CustomerCode $customer_code)
    {
        $this->customer_code = $customer_code;
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
            'customer_name' => ['bail', 'nullable', 'string', 'max:50']
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
            'customer_code' => trans('view.master.customers.customer_code'),
            'customer_name' => trans('view.master.customers.customer_name')
        ];
    }
}
