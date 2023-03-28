<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;

class SearchShipmentFixRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     *  @var \App\Models\Master\Customer
     */
    private $customer;

    /**
     * @var \App\Models\Master\EndUser
     */
    private $end_user;

    /**
     * @var \App\Models\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return void
     */
    public function __construct(
        Factory $factory,
        Customer $customer,
        EndUser $end_user,
        DeliveryDestination $delivery_destination
    ) {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user = $end_user;
        $this->delivery_destination = $delivery_destination;
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
            'end_user_code' => ['bail', 'nullable', "exists:{$this->end_user->getTable()}"],
            'delivery_destination_code' => ['bail', 'nullable', "exists:{$this->delivery_destination->getTable()}"],
            'shipping_date_from' => ['bail', 'required', 'date'],
            'shipping_date_to' => ['bail', 'required', 'date', 'after_or_equal:shipping_date_from'],
            'delivery_date_from' => ['bail', 'nullable', 'date'],
            'delivery_date_to' => ['bail', 'nullable', 'date', 'after_or_equal:delivery_date_from'],
            'order_number' => ['bail', 'nullable', 'string', 'max:14'],
            'base_plus_order_number' => ['bail', 'nullable', 'string', 'max:10'],
            'base_plus_order_chapter_number' => ['bail', 'nullable', 'string', 'max:3']
        ];
    }
}
