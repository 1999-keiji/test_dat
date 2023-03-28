<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;

class SaveCalendarRequest extends FormRequest
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
            'date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
            ],
            'event' => [
                'bail',
                'required',
                'string',
                'max:30',
            ],
            'remark' => [
                'bail',
                'nullable',
                'string',
                'max:50',
            ]
        ];
    }
}
