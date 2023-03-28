<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use App\Http\Requests\FormRequest;
use App\ValueObjects\Enum\DisplayKubun;

class EditSearchGrowthSimulationRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\Enum\DisplayKubun
     */
    private $display_kubun;

    /**
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @return void
     */
    public function __construct(DisplayKubun $display_kubun)
    {
        $this->display_kubun = $display_kubun;
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
            'display_term' => ['bail', 'required', 'in:date,month'],
            'display_from_date' => ['bail', 'required_if:display_term,date', 'date_format:Y/m/d'],
            'week_term' => ['bail', 'required_if:display_term,date', 'in:1,2,3'],
            'display_from_month' => ['bail', 'required_if:display_term,month', 'date_format:Y/m'],
            'display_kubun' => ['bail', 'required', 'in:'.implode(',', $this->display_kubun->all())]
        ];
    }
}
