<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;
use App\ValueObjects\Decimal\OrderUnit;

class SaveSlipRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Model\Master\Customer
     */
    private $customer;

    /**
     * @var \App\Model\Master\EndUser
     */
    private $end_user;

    /**
     * @var \App\Model\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Models\Master\Currency
     */
    private $currency;

    /**
     * @var \App\ValueObjects\Decimal\OrderUnit
     */
    private $order_unit;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\Currency $currency
     * @param  \App\ValueObjects\Decimal\OrderUnit $order_unit
     * @return void
     */
    public function __construct(
        Factory $factory,
        Customer $customer,
        EndUser $end_user,
        DeliveryDestination $delivery_destination,
        Currency $currency,
        OrderUnit $order_unit
    ) {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user = $end_user;
        $this->delivery_destination = $delivery_destination;
        $this->currency = $currency;
        $this->order_unit = $order_unit;
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
            'customer_code' => ['bail', 'required', "exists:{$this->customer->getTable()}"],
            'delivery_date' => ['bail', 'required', 'date_format:Y/m/d'],
            'end_user_code' => ['bail', 'required', "exists:{$this->end_user->getTable()}"],
            'delivery_destination_code' => ['bail', 'required', "exists:{$this->delivery_destination->getTable()}"],
            'currency_code'  => ['bail', 'required', "exists:{$this->currency->getTable()}"],
            'product_name' => ['bail', 'required', 'string', 'max:40'],
            'order_quantity' => ['bail', 'required', 'integer', 'min:0', 'max:99999'],
            'order_unit' => [
                'bail',
                'required',
                'numeric',
                "min:-99999999.99999",
                "max:{$this->order_unit->getMaximumNum()}"
            ],
            'order_message' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }
}
