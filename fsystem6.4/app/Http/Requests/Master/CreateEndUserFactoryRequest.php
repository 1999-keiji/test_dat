<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\EndUser;
use App\Models\Master\EndUserFactory;
use App\Models\Master\Factory;

class CreateEndUserFactoryRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\EndUser
     */
    private $end_user;

    /**
     *  @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     *  @var \App\Models\Master\EndUserFactory
     */
    private $end_user_factory;

    /**
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\EndUserFactory $end_user_factory
     * @return void
     */
    public function __construct(EndUser $end_user, Factory $factory, EndUserFactory $end_user_factory)
    {
        $this->end_user = $end_user;
        $this->factory = $factory;
        $this->end_user_factory = $end_user_factory;
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
            'end_user_code' => [
                'bail',
                'required',
                "exists:{$this->end_user->getTable()}"
            ],
            'factory_code' => [
                'bail',
                'required',
                "exists:{$this->factory->getTable()}",
                Rule::unique($this->end_user_factory->getTable())->where(function ($query) {
                    return $query->where('end_user_code', $this->end_user_code);
                })
            ]
        ];
    }
}
