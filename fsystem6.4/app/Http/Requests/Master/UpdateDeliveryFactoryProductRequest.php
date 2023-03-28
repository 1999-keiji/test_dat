<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Currency;
use App\ValueObjects\Date\ApplicationEndedOn;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Decimal\UnitPrice;

class UpdateDeliveryFactoryProductRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Currency
     */
    private $currency;

    /**
     * @var \App\ValueObjects\Date\ApplicationStartedOn
     */
    private $application_started_on;

    /**
     * @var \App\ValueObjects\Date\ApplicationEndedOn
     */
    private $application_ended_on;

    /**
     * @var \App\ValueObjects\String\UnitPrice
     */
    private $unit_price;

    /**
     * @param  \App\Models\Master\Currency $currency
    * @param  \App\ValueObjects\Date\ApplicationStartedOn $application_started_on
     * @param  \App\ValueObjects\Date\ApplicationEndedOn $application_ended_on
     * @param  \App\ValueObjects\String\UnitPrice $unit_price
     * @return void
     */
    public function __construct(
        Currency $currency,
        ApplicationStartedOn $application_started_on,
        ApplicationEndedOn $application_ended_on,
        UnitPrice $unit_price
    ) {
        $this->currency = $currency;
        $this->application_started_on = $application_started_on;
        $this->application_ended_on = $application_ended_on;
        $this->unit_price = $unit_price;
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
            'application_started_on.*' => [
                'bail',
                'required',
                "date_format:{$this->application_started_on->getDateFormat()}"
            ],
            'application_ended_on.*' => [
                'bail',
                'required',
                "date_format:{$this->application_ended_on->getDateFormat()}",
                'after_or_equal:application_started_on.*'
            ],
            'unit_price.*' => [
                'bail',
                'required',
                "min:{$this->unit_price->getMinimumNum()}",
                "max:{$this->unit_price->getMaximumNum()}",
                "regex:{$this->unit_price->getRegexPattern()}"
            ],
            'currency_code.*' => [
                'bail',
                'required',
                "exists:{$this->currency->getTable()},currency_code"
            ]
        ];
    }
}
