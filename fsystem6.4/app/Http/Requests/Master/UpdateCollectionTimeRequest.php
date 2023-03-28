<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;

class UpdateCollectionTimeRequest extends FormRequest
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
            'collection_time' => ['bail', 'required', 'string', 'max:20'],
            'remark' => ['bail', 'nullable', 'string', 'max:100'],
        ];
    }
}
