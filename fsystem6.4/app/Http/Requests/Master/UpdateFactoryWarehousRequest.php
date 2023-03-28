<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;

class UpdateFactoryWarehousRequest extends FormRequest
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
            'priorities' => [
                'bail',
                'required',
                'array',
                'size:'.$this->route('factory')->factory_warehouses->count()
            ],
            'priorities.*' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'max:'.$this->route('factory')->factory_warehouses->count(),
                'distinct'
            ]
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
            'priorities.*' => trans('view.master.factories.priority')
        ];
    }
}
