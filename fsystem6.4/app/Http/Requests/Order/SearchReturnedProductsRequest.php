<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Models\Master\EndUser;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\Product;

class SearchReturnedProductsRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Model\Master\EndUser
     */
    private $end_user;

    /**
     * @var \App\Model\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Model\Master\Product
     */
    private $product;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\Product $product
     * @return void
     */
    public function __construct(
        Factory $factory,
        EndUser $end_user,
        DeliveryDestination $delivery_destination,
        Product $product
    ) {
        $this->factory = $factory;
        $this->end_user = $end_user;
        $this->delivery_destination = $delivery_destination;
        $this->product = $product;
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
            'received_date' => ['bail', 'nullable', 'date_format:Y/m/d'],
            'delivery_date' => ['bail', 'nullable', 'date_format:Y/m/d'],
            'end_user_code' => ['bail', 'nullable', "exists:{$this->end_user->getTable()}"],
            'delivery_destination_code' => ['bail', 'nullable', "exists:{$this->delivery_destination->getTable()}"],
            'product_code' => ['bail', 'nullable', "exists:{$this->product->getTable()}"],
            'order_number' => ['bail', 'nullable', 'string', 'max:14'],
            'base_plus_order_number' => ['bail', 'nullable', 'string', 'max:10'],
            'base_plus_order_chapter_number' => ['bail', 'nullable', 'string', 'max:3']
        ];
    }
}
