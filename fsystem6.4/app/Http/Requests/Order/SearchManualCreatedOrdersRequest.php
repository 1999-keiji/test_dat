<?php
declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Customer;
use App\Models\Master\EndUserFactory;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\Factory;

class SearchManualCreatedOrdersRequest extends FormRequest
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
     * @var \App\Model\Master\EndUserFactory
     */
    private $end_user_factory;

    /**
     * @var \App\Model\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Model\Master\DeliveryFactoryProduct
     */
    private $delivery_factory_product;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\Models\Master\EndUserFactory $end_user_factory
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @return void
     */
    public function __construct(
        Factory $factory,
        Customer $customer,
        EndUserFactory $end_user_factory,
        DeliveryDestination $delivery_destination,
        DeliveryFactoryProduct $delivery_factory_product
    ) {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user_factory = $end_user_factory;
        $this->delivery_destination = $delivery_destination;
        $this->delivery_factory_product = $delivery_factory_product;
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
            'received_date' => ['bail', 'required', 'date_format:Y/m/d'],
            'customer_code' => ['bail', 'required', "exists:{$this->customer->getTable()}"],
            'end_user_code' => [
                'bail',
                'required',
                Rule::exists($this->end_user_factory->getTable())->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'delivery_destination_code' => ['bail', 'required', "exists:{$this->delivery_destination->getTable()}"],
            'factory_product_sequence_number' => [
                'bail',
                'nullable',
                Rule::exists($this->delivery_factory_product->getTable())->where(function ($query) {
                    $query->where('delivery_destination_code', $this->delivery_destination_code)
                        ->where('factory_code', $this->factory_code);
                })
            ]
        ];
    }
}
