<?php

declare(strict_types=1);

namespace App\Http\Requests\FactoryProductionWork;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\ValueObjects\Enum\PanelStatus;

class UpdateActivityResultsRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\Enum\PanelStatus
     */
    private $panel_status;

    /**
     * @param  \App\ValueObjects\Enum\PanelStatus
     * @return void
     */
    public function __construct(PanelStatus $panel_status)
    {
        $this->panel_status = $panel_status;
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
        $factory = $this->route('factory_species')->factory;
        return [
            'row' => ['bail', 'required', 'integer', 'min:1', "max:{$factory->number_of_rows}"],
            'column' => ['bail', 'required', 'integer', 'min:1', "max:{$factory->number_of_columns}"],
            'panel_status.*' => ['bail', 'required', Rule::in($this->panel_status->all())],
            'using_hole_count.*' => [
                'bail',
                'nullable',
                'integer',
                'min:0',
                'max:999',
                'required_if:panel_status.*,'.PanelStatus::OPERATION
            ]
        ];
    }
}
