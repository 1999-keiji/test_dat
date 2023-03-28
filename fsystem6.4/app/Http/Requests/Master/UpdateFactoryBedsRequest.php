<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;

class UpdateFactoryBedsRequest extends FormRequest
{
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
            'number_of_floors' => ['bail', 'required', 'integer', 'min:0', 'max:9'],
            'number_of_columns' => ['bail', 'required', 'integer', 'confirmed'],
            'number_of_circulation' => ['bail', 'required', 'integer', 'min:0', 'max:99'],
            'factory_columns.*.column_name' => ['bail', 'required', 'string', 'max:5'],
            'factory_beds.*.*.*.x_coordinate_panel' => ['bail', 'required', 'integer', 'min:0', 'max:99'],
            'factory_beds.*.*.*.y_coordinate_panel' => ['bail', 'required', 'integer', 'min:0', 'max:99'],
            'factory_beds.*.*.*.*.irradiation' => ['bail', 'nullable', 'string','max:5'],
            'circulations.*' => ['bail', 'required', 'integer', 'min:0', 'max:99']
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
            'number_of_columns.confirmed' => '列数と循環あたりの列数の合計を一致させてください。'
        ];
    }
}
