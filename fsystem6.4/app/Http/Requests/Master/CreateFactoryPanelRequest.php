<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryPanel;

class CreateFactoryPanelRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\FactoryPanel
     */
    private $factory_panel;

    /**
     * @param \App\Models\Master\FactoryPanel $factory_panel
     * @return void
     */
    public function __construct(FactoryPanel $factory_panel)
    {
        $this->factory_panel = $factory_panel;
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
            'number_of_holes' => [
                'bail',
                'required',
                'integer',
                'min:0',
                Rule::unique($this->factory_panel->getTable())->where(function ($query) {
                    return $query->where('factory_code', $this->factory_code);
                })
            ]
        ];
    }
}
