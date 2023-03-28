<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\Models\Master\User;
use App\ValueObjects\Enum\Affiliation;
use App\ValueObjects\Enum\Permission;
use App\ValueObjects\String\UserCode;

class CreateUserRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\user
     */
    private $user;

    /**
     * @var \App\Model\Master\Factory
     */
    private $factory;

    /**
     * @var \App\ValueObjects\String\UserCode
     */
    private $user_code;

    /**
     * @var \App\ValueObjects\Enum\Affiliation
     */
    private $affiliation;

    /**
     * @var \App\ValueObjects\Enum\Permission
     */
    private $permission;

    /**
     * @param  \App\Models\Master\User $user
     * @param  \App\Model\Master\Factory $factory
     * @param  \App\ValueObjects\String\UserCode $user_code
     * @param  \App\ValueObjects\Enum\Affiliation $affiliation
     * @param  \App\ValueObjects\Enum\Permission $permission
     * @return void
     */
    public function __construct(
        User $user,
        Factory $factory,
        UserCode $user_code,
        Affiliation $affiliation,
        Permission $permission
    ) {
        $this->user = $user;
        $this->factory = $factory;
        $this->user_code = $user_code;
        $this->affiliation = $affiliation;
        $this->permission = $permission;
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
            'user_code' => [
                'bail',
                'required',
                "unique:{$this->user->getTable()}",
                "max:{$this->user_code->getMaxLength()}",
                "regex:{$this->user_code->getRegexPattern()}"
            ],
            'user_name' => ['bail', 'required', 'string', 'max:30'],
            'affiliation' => [
                'bail',
                'required',
                Rule::in($this->affiliation->all())
            ],
            'factory_code' => [
                'bail',
                'required_if:affiliation,'.implode(',', $this->affiliation->getAffiliationsOfFactory()),
                'array'
            ],
            'factory_code.*' => [
                "exists:{$this->factory->getTable()},factory_code"
            ],
            'mail_address' => ['bail', 'required', 'max:250', 'email'],
            'permissions' => ['bail', 'required', 'array'],
            'permissions.*' => [
                Rule::in($this->permission->all())
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'factory_code.required_if' => '工場所属の場合、必ず工場を指定してください。'
        ];
    }
}
