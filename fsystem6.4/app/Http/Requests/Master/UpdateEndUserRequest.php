<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\EndUser;
use App\ValueObjects\Enum\AbroadShipmentPriceShowClass;
use App\ValueObjects\Enum\ExportManagingClass;
use App\ValueObjects\Enum\ExportExchangeRateCode;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass;
use App\ValueObjects\Enum\StatementOfDeliveryClass;
use App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass;
use App\ValueObjects\Enum\StatementOfDeliveryRemarkClass;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\EndUserCode;
use App\ValueObjects\String\PostalCode;

class UpdateEndUserRequest extends FormRequest
{
    /**
     *  @var \App\Models\Master\Customer
     */
    private $customer;

    /**
     * @var \App\Models\Master\Currency
     */
    private $currency;

    /**
     *  @var \App\Models\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\ValueObjects\String\EndUserCode
     */
    private $end_user_code;

    /**
     *  @var \App\ValueObjects\String\CountryCode
     */
    private $country_code;

    /**
     *  @var \App\ValueObjects\Enum\PrefectureCode
     */
    private $prefecture_code;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryClass
     */
    private $statement_of_delivery_class;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass
     */
    private $statement_of_delivery_price_show_class;

    /**
     * @var \App\ValueObjects\Enum\AbroadShipmentPriceShowClass
     */
    private $abroad_shipment_price_show_class;

    /**
     * @var \App\ValueObjects\Enum\ExportManagingClass
     */
    private $export_managing_class;

    /**
     * @var \App\ValueObjects\Enum\ExportExchangeRateCode
     */
    private $export_exchange_rate_code;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryRemarkClass
     */
    private $statement_of_delivery_remark_class;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass
     */
    private $statement_of_delivery_buyer_remark_class;

    /**
     * @var array
     */
    protected $on_off_checkboxes;

    /**
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\Models\Master\Currency $currency
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\ValueObjects\String\EndUserCode $end_user_code
     * @param  \App\ValueObjects\String\CountryCode $country_code
     * @param  \App\ValueObjects\String\PostalCode $postal_code
     * @param  \App\ValueObjects\Enum\PrefectureCode $prefecture_code
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryClass $statement_of_delivery_class
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass $statement_of_delivery_price_show_class
     * @param  \App\ValueObjects\Enum\AbroadShipmentPriceShowClass $abroad_shipment_price_show_class
     * @param  \App\ValueObjects\Enum\ExportManagingClass $export_managing_class
     * @param  \App\ValueObjects\Enum\ExportExchangeRateCode $export_exchange_rate_code
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryRemarkClass $statement_of_delivery_remark_class
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass $statement_of_delivery_buyer_remark_class
     * @return void
     */
    public function __construct(
        EndUser $end_user,
        Customer $customer,
        Currency $currency,
        DeliveryDestination $delivery_destination,
        EndUserCode $end_user_code,
        CountryCode $country_code,
        PostalCode $postal_code,
        PrefectureCode $prefecture_code,
        StatementOfDeliveryClass $statement_of_delivery_class,
        StatementOfDeliveryPriceShowClass $statement_of_delivery_price_show_class,
        AbroadShipmentPriceShowClass $abroad_shipment_price_show_class,
        ExportManagingClass $export_managing_class,
        ExportExchangeRateCode $export_exchange_rate_code,
        StatementOfDeliveryRemarkClass $statement_of_delivery_remark_class,
        StatementOfDeliveryBuyerRemarkClass $statement_of_delivery_buyer_remark_class
    ) {
        $this->end_user = $end_user;
        $this->customer = $customer;
        $this->currency = $currency;
        $this->delivery_destination = $delivery_destination;
        $this->end_user_code = $end_user_code;
        $this->country_code = $country_code;
        $this->postal_code = $postal_code;
        $this->prefecture_code = $prefecture_code;
        $this->statement_of_delivery_class = $statement_of_delivery_class;
        $this->statement_of_delivery_price_show_class = $statement_of_delivery_price_show_class;
        $this->abroad_shipment_price_show_class = $abroad_shipment_price_show_class;
        $this->export_managing_class = $export_managing_class;
        $this->export_exchange_rate_code = $export_exchange_rate_code;
        $this->statement_of_delivery_remark_class = $statement_of_delivery_remark_class;
        $this->statement_of_delivery_buyer_remark_class = $statement_of_delivery_buyer_remark_class;

        $this->on_off_checkboxes = $end_user->getWillCastAsBoolean();
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
        $updatable = $this->route('end_user')->creating_type->getUpdatableCreatingTypes();
        $rules = [
            'customer_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                "exists:{$this->customer->getTable()}"
            ],
            'end_user_name' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:50'
            ],
            'end_user_name2' => ['bail', 'nullable', 'string', 'max:50'],
            'end_user_abbreviation' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:20'
            ],
            'end_user_name_kana' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:30',
                'hankana'
            ],
            'end_user_name_english' => [
                'bail',
                'nullable',
                'string',
                'max:65',
                'alpha_period_dash'
            ],
            'country_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                "min:{$this->country_code->getMinLength()}",
                "max:{$this->country_code->getMaxLength()}",
                "regex:{$this->country_code->getRegexPattern()}"
            ],
            'postal_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                "max:{$this->postal_code->getMaxLength()}",
                "regex:{$this->postal_code->getRegexPattern()}"
            ],
            'prefecture_code' => [
                'bail',
                'nullable',
                'required_if:creating_type,'.implode(',', $updatable),
                "required_if:country_code,{$this->prefecture_code->getJoinedRequirePrefectureCodeList()}",
                Rule::in($this->prefecture_code->all())
            ],
            'address' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:50'
            ],
            'address2' => ['bail', 'nullable', 'string', 'max:50'],
            'address3' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address2' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address3' => ['bail', 'nullable', 'string', 'max:50'],
            'phone_number' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'max:20',
                'regex:/\A[0-9-]+\z/'
            ],
            'mail_address' => ['bail', 'nullable', 'max:250', 'email'],
            'end_user_staff_name' => ['bail', 'nullable', 'string', 'max:30'],
            'currency_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                "exists:{$this->currency->getTable()}"
            ],
            'delivery_destination_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                "exists:{$this->delivery_destination->getTable()}"
            ],
            'seller_code' => ['bail', 'nullable', 'string', 'max:8', 'regex:/\A[A-Z0-9]+\z/'],
            'seller_name' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'max:30'
            ],
            'pickup_slip_message' => ['bail', 'nullable', 'max:40'],
            'statement_of_delivery_class' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->statement_of_delivery_buyer_remark_class->all())
            ],
            'statement_of_delivery_price_show_class' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->statement_of_delivery_price_show_class->all())
            ],
            'abroad_shipment_price_show_class' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->abroad_shipment_price_show_class->all())
            ],
            'export_managing_class' => [
                'bail',
                'nullable',
                Rule::in($this->export_managing_class->all())
            ],
            'export_exchange_rate_code' => [
                'bail',
                'nullable',
                Rule::in($this->export_exchange_rate_code->all())
            ],
            'remarks1' => ['bail', 'nullable', 'string', 'max:50'],
            'remarks2' => ['bail', 'nullable', 'string', 'max:50'],
            'remarks3' => ['bail', 'nullable', 'string', 'max:50'],
            'remarks4' => ['bail', 'nullable', 'string', 'max:50'],
            'remarks5' => ['bail', 'nullable', 'string', 'max:50'],
            'remarks6' => ['bail', 'nullable', 'string', 'max:50'],
            'loading_port_code' => ['bail', 'nullable', 'string', 'max:4'],
            'loading_port_name' => ['bail', 'nullable', 'string', 'max:30'],
            'drop_port_code' => ['bail', 'nullable', 'string', 'max:4'],
            'drop_port_name' => ['bail', 'nullable', 'string', 'max:30'],
            'exchange_rate_port_code' => ['bail', 'nullable', 'string', 'max:4'],
            'exchange_rate_port_name' => ['bail', 'nullable', 'string', 'max:30'],
            'lot_managing_target_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'end_user_remark' => ['bail', 'nullable', 'max:50'],
            'end_user_request_number' => ['bail', 'nullable', 'max:5'],
            'statement_of_delivery_remark_class' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->statement_of_delivery_remark_class->all())
            ],
            'statement_of_delivery_buyer_remark_class' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->statement_of_delivery_buyer_remark_class->all())
            ],
            'export_target_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'group_company_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'company_code' => ['bail', 'nullable', 'string', 'max:6', 'regex:/\A[A-Z0-9]+\z/'],
            'company_name' => ['bail', 'nullable', 'string', 'max:50'],
            'company_abbreviation' => ['bail', 'nullable', 'string', 'max:20'],
            'company_name_kana' => ['bail', 'nullable', 'string', 'max:30', 'hankana'],
            'company_name_english' => ['bail', 'nullable', 'string', 'max:50', 'regex:alpha_period_dash'],
            'company_group_code' => ['bail', 'nullable', 'integer'],
            'company_group_name' => ['bail', 'nullable', 'string', 'max:40'],
            'company_group_name_english' => ['bail', 'nullable', 'string', 'max:50', 'alpha_period_dash'],
            'remark' => ['bail', 'nullable', 'string', 'max:255']
        ];

        return $rules + $this->reservedRules();
    }
}
