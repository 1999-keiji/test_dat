<?php
declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\ValueObjects\Enum\Affiliation;
use App\ValueObjects\String\UserCode;

class SearchUsersRequest extends FormRequest
{
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
     * @param \App\Model\Master\Factory $factory
     * @param \App\ValueObjects\String\UserCode $user_code
     * @param \App\ValueObjects\Enum\Affiliation $affiliation
     */
    public function __construct(Factory $factory, UserCode $user_code, Affiliation $affiliation)
    {
        $this->factory = $factory;
        $this->user_code = $user_code;
        $this->affiliation = $affiliation;
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
                'nullable',
                'string',
                "max:{$this->user_code->getMaxLength()}",
                "regex:{$this->user_code->getRegexPattern()}"
            ],
            'user_name' => ['bail', 'nullable', 'string', 'max:30'],
            'affiliation' => [
                'bail',
                'nullable',
                Rule::in($this->affiliation->all())
            ],
            'factory_code' => ['bail', 'nullable', "exists:{$this->factory->getTable()}"]
        ];
    }
}
