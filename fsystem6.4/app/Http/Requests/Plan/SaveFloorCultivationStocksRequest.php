<?php

namespace App\Http\Requests\Plan;

use App\Http\Requests\FormRequest;

class SaveFloorCultivationStocksRequest extends FormRequest
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
            'moving_panel_count_pattern.*.*.*' => ['bail', 'nullable', 'integer', 'min:0', 'max:99'],
            'moving_bed_count_floor_pattern.*.*.*' => ['bail', 'nullable', 'integer', 'min:-99', 'max:99']
        ];
    }
}
