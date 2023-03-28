<?php

declare(strict_types=1);

namespace App\Http\Requests\FactoryProductionWork;

use App\Http\Requests\FormRequest;
use App\ValueObjects\Date\WorkingDate;

class SearchPanelActivityResultsRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\Date\WorkingDate
     */
    private $working_date;

    /**
     *
     * @param  \App\ValueObjects\Date\WorkingDate
     * @return void
     */
    public function __construct(WorkingDate $working_date)
    {
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
        $factory = $this->route('factory_species')->factory;
        return [
            'working_date' => ['bail', 'nullable', "date_format:{$this->working_date->getDateFormat()}"],
            'row' => ['bail', 'nullable', 'integer', 'min:1', "max:{$factory->number_of_rows}"],
            'column' => ['bail', 'nullable', 'integer', 'min:1', "max:{$factory->number_of_columns}"]
        ];
    }
}
