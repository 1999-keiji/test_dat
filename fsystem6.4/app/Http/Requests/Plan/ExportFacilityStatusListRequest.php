<?php

namespace App\Http\Requests\Plan;

use App\Http\Requests\FormRequest;
use App\Models\Master\Factory;
use App\ValueObjects\Date\WorkingDate;

class ExportFacilityStatusListRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\ValueObjects\Date\WorkingDate
     */
    private $working_date;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\ValueObjects\Date\WorkingDate
     * @return void
     */
    public function __construct(Factory $factory, WorkingDate $working_date)
    {
        $this->factory = $factory;
        $this->working_date = $working_date;
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
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'working_date' => ['bail', 'required', "date_format:{$this->working_date->getDateFormat()}"]
        ];
    }
}
