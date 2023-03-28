<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryCyclePattern;

class UpdateFactoryCyclePatternRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\FactoryCyclePattern
     */
    private $factory_cycle_pattern;

    /**
     * @param  \App\Models\Master\FactoryCyclePattern
     * @return void
     */
    public function __construct(FactoryCyclePattern $factory_cycle_pattern)
    {
        $this->factory_cycle_pattern = $factory_cycle_pattern;
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
            'sequence_number' => [
                'bail',
                'nullable',
                Rule::exists($this->factory_cycle_pattern->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->route('factory')->factory_code);
                })
            ],
            'cycle_pattern_name' => ['bail', 'required', 'string', 'max:50'],
            'pattern.*' => ['bail', 'required', 'string', 'max:1'],
            'number_of_panels.*.*' => ['bail', 'required', 'integer', 'min:0', 'max:99']
        ];
    }
}
