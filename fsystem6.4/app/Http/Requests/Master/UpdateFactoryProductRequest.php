<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Currency;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Decimal\UnitPrice;
use App\ValueObjects\Decimal\Cost;

class UpdateFactoryProductRequest extends FormRequest
{
    /**
     *  @var \App\Models\Master\Currency
     */
    private $currency;

    /**
     * @var \App\ValueObjects\Date\ApplicationStartedOn
     */
    private $application_started_on;

    /**
     * @var \App\ValueObjects\String\UnitPrice
     */
    private $unit_price;

    /**
     * @var \App\ValueObjects\String\Cost
     */
    private $cost;

    /**
     * @param  \App\Models\Master\Currency $currency
     * @param  \App\ValueObjects\Date\ApplicationStartedOn $application_started_on
     * @param  \App\ValueObjects\Decimal\UnitPrice $unit_price
     * @param  \App\ValueObjects\Decimal\Cost $cost
     * @return void
     */
    public function __construct(
        Currency $currency,
        ApplicationStartedOn $application_started_on,
        UnitPrice $unit_price,
        Cost $cost
    ) {
        $this->currency = $currency;
        $this->application_started_on = $application_started_on;
        $this->unit_price = $unit_price;
        $this->cost = $cost;
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
            'factory_product_name' => ['bail', 'required', 'string', 'max:50'],
            'factory_product_abbreviation' => ['bail', 'required', 'string', 'max:15'],
            'number_of_heads' => ['bail', 'required', 'numeric', 'max:9999'],
            'weight_per_number_of_heads' => ['bail', 'required', 'numeric', 'max:99999'],
            'input_group' => [
                'bail',
                'required',
                Rule::in(array_keys(config('constant.master.factory_products.input_group')))
            ],
            'number_of_cases' => ['bail', 'required', 'numeric', 'max:99999'],
            'unit' => [
                'bail',
                'required',
                Rule::in(config('constant.master.factory_products.unit'))
            ],
            'remark' => ['bail', 'nullable', 'string', 'max:255'],
            'factory_product_prices' => ['bail', 'required', 'array'],
            'factory_product_prices.*.application_started_on' => [
                'bail',
                'required_with_all:unit_price.*,cost.*,currency_code.*',
                "date_format:{$this->application_started_on->getDateFormat()}"
            ],
            'factory_product_prices.*.unit_price' => [
                'bail',
                'required_with_all:application_started_on.*,cost.*,currency_code.*',
                "min:{$this->unit_price->getMinimumNum()}",
                "max:{$this->unit_price->getMaximumNum()}",
                "regex:{$this->unit_price->getRegexPattern()}"
            ],
            'factory_product_prices.*.cost' => [
                'bail',
                'required_with_all:application_started_on.*,unit_price.*,currency_code.*',
                "min:{$this->cost->getMinimumNum()}",
                "max:{$this->cost->getMaximumNum()}",
                "regex:{$this->cost->getRegexPattern()}"
            ],
            'factory_product_prices.*.currency_code' => [
                'bail',
                'required_with_all:application_started_on.*,unit_price.*,cost.*',
                "exists:{$this->currency->getTable()},currency_code"
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
            'factory_product_prices.*.application_started_on' => trans('view.master.global.application_started_on'),
            'factory_product_prices.*.unit_price' => trans('view.master.global.unit_price'),
            'factory_product_prices.*.cost' => trans('view.master.global.cost'),
            'factory_product_prices.*.currency_code' => trans('view.master.global.currency_code')
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'factory_product_prices.required' => '工場商品価格は必ず設定してください。'
        ];
    }
}
