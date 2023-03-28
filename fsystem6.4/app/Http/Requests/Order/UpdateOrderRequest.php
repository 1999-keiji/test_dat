<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Currency;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\EndUser;
use App\Models\Master\TransportCompany;
use App\Models\Master\CollectionTime;
use App\ValueObjects\Decimal\OrderUnit;
use App\ValueObjects\Decimal\OrderAmount;
use App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass;
use App\ValueObjects\Enum\BasisForRecordingSalesClass;
use App\ValueObjects\Enum\SmallPeaceOfPeperTypeCode;

class UpdateOrderRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\EndUser
     */
    private $end_user;

    /**
     * @var \App\Model\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Model\Master\DeliveryFactoryProduct
     */
    private $delivery_factory_product;

    /**
     * @var \App\Models\Master\Currency
     */
    private $currency;

    /**
     * @var \App\Model\Master\TransportCompany
     */
    private $transport_company;

    /**
     * @var \App\Model\Master\CollectionTime
     */
    private $collection_time;

    /**
     * @var \App\ValueObjects\Decimal\OrderUnit
     */
    private $order_unit;

    /**
     * @var \App\ValueObjects\Decimal\OrderAmount
     */
    private $order_amount;

    /**
     * @var \App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass
     */
    private $statement_delivery_price_display_class;

    /**
     * @var \App\ValueObjects\Enum\BasisForRecordingSalesClass
     */
    private $basis_for_recording_sales_class;

    /**
     * @var \App\ValueObjects\Enum\SmallPeaceOfPeperTypeCode
     */
    private $small_peace_of_peper_type_code;

    /**
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Model\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @param  \App\Models\Master\Currency $currency
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @param  \App\ValueObjects\Decimal\OrderUnit $order_unit
     * @param  \App\ValueObjects\Decimal\OrderAmount $order_amount
     * @param  \App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass $statement_delivery_price_display_class
     * @param  \App\ValueObjects\Enum\BasisForRecordingSalesClass $basis_for_recording_sales_class
     * @param  \App\ValueObjects\Enum\SmallPeaceOfPeperTypeCode $small_peace_of_peper_type_code
     * @return void
     */
    public function __construct(
        EndUser $end_user,
        DeliveryDestination $delivery_destination,
        DeliveryFactoryProduct $delivery_factory_product,
        Currency $currency,
        TransportCompany $transport_company,
        CollectionTime $collection_time,
        OrderUnit $order_unit,
        OrderAmount $order_amount,
        StatementDeliveryPriceDisplayClass $statement_delivery_price_display_class,
        BasisForRecordingSalesClass $basis_for_recording_sales_class,
        SmallPeaceOfPeperTypeCode $small_peace_of_peper_type_code
    ) {
        $this->end_user = $end_user;
        $this->delivery_destination = $delivery_destination;
        $this->delivery_factory_product = $delivery_factory_product;
        $this->currency = $currency;
        $this->transport_company = $transport_company;
        $this->collection_time = $collection_time;
        $this->order_unit = $order_unit;
        $this->order_amount = $order_amount;
        $this->statement_delivery_price_display_class = $statement_delivery_price_display_class;
        $this->basis_for_recording_sales_class = $basis_for_recording_sales_class;
        $this->small_peace_of_peper_type_code = $small_peace_of_peper_type_code;
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
            'end_user_order_number' => ['bail', 'nullable', 'string', 'max:17'],
            'received_date' => ['bail', 'required', 'date_format:Y/m/d'],
            'delivery_date' => ['bail', 'required', 'date_format:Y/m/d'],
            'shipping_date' => ['bail', 'required', 'date_format:Y/m/d', 'before_or_equal:delivery_date'],
            'end_user_code' => ['bail', 'required', "exists:{$this->end_user->getTable()}"],
            'delivery_destination_code' => ['bail', 'required', "exists:{$this->delivery_destination->getTable()}"],
            'factory_product_sequence_number' => [
                'bail',
                'required',
                Rule::exists($this->delivery_factory_product->getTable())->where(function ($query) {
                    $query->where('delivery_destination_code', $this->delivery_destination_code)
                        ->where('factory_code', $this->route('order')->factory_code);
                })
            ],
            'product_name' => ['bail', 'required', 'string', 'max:50'],
            'supplier_product_name' => ['bail', 'nullable', 'string', 'max:50'],
            'customer_product_name' => ['bail', 'nullable', 'string', 'max:50'],
            'order_quantity' => ['bail', 'required', 'integer', 'min:1', 'max:99999'],
            'place_order_unit_code' => ['bail', 'required', 'string', 'max:5'],
            'order_unit' => [
                'bail',
                'required',
                "min:{$this->order_unit->getMinimumNum()}",
                "max:{$this->order_unit->getMaximumNum()}",
                "regex:{$this->order_unit->getRegexPattern()}"
            ],
            'order_amount' => [
                'bail',
                'nullable',
                "min:{$this->order_amount->getMinimumNum()}",
                "max:{$this->order_amount->getMaximumNum()}",
                "regex:{$this->order_amount->getRegexPattern()}"
            ],
            'currency_code'  => ['bail', 'required', "exists:{$this->currency->getTable()}"],
            'statement_delivery_price_display_class' => [
                'bail',
                'required',
                Rule::in($this->statement_delivery_price_display_class->all())
            ],
            'basis_for_recording_sales_class' => [
                'bail',
                'required',
                Rule::in($this->basis_for_recording_sales_class->all())
            ],
            'recived_order_unit' => [
                'bail',
                'nullable',
                "min:{$this->order_unit->getMinimumNum()}",
                "max:{$this->order_unit->getMaximumNum()}",
                "regex:{$this->order_unit->getRegexPattern()}"
            ],
            'customer_received_order_unit' => [
                'bail',
                'nullable',
                "min:{$this->order_amount->getMinimumNum()}",
                "max:{$this->order_amount->getMaximumNum()}",
                "regex:{$this->order_amount->getRegexPattern()}"
            ],
            'small_peace_of_peper_type_code' => [
                'bail',
                'required',
                Rule::in($this->small_peace_of_peper_type_code->all())
            ],
            'transport_company_code' => [
                'bail',
                'nullable',
                "exists:{$this->transport_company->getTable()}"
            ],
            'collection_time_sequence_number' => [
                'bail',
                'nullable',
                Rule::exists($this->collection_time->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('transport_company_code', $this->transport_company_code);
                })
            ],
            'own_company_code' => ['bail', 'nullable', 'string', 'max:6'],
            'organization_name' => ['bail', 'nullable', 'string', 'max:40'],
            'base_plus_end_user_code' => ['bail', 'nullable', 'string', 'max:10'],
            'customer_staff_name' => ['bail', 'nullable', 'string', 'max:30'],
            'purchase_staff_name' => ['bail', 'nullable', 'string', 'max:30'],
            'place_order_work_staff_name' => ['bail', 'nullable', 'string', 'max:30'],
            'seller_name' => ['bail', 'nullable', 'string', 'max:30'],
            'order_message' => ['bail', 'nullable', 'string', 'max:50'],
            'updated_at' =>  ['bail', 'required']
        ];
    }
}
