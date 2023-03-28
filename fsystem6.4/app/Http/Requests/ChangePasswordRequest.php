<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\Password
     */
    private $password;

    /**
     *
     * @param  \App\ValueObjects\String\Password $password
     * @return void
     */
    public function __construct(Password $password)
    {
        $this->password = $password;
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
            'current_password' => ['bail', 'required'],
            'password'         => [
                'bail',
                'required',
                'confirmed',
                "min:{$this->password->getMinLength()}",
                "regex:{$this->password->getRegexPattern()}"
            ],
        ];
    }
}
