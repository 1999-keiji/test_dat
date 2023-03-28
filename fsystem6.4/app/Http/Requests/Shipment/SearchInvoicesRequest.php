<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;
use App\Models\Master\Customer;
use App\Models\Master\Factory;

class SearchInvoicesRequest extends FormRequest
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
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\Customer $customer
     * @return void
     */
    public function __construct(Factory $factory, Customer $customer)
    {
        $this->factory = $factory;
        $this->customer = $customer;
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
            'delivery_month' => ['bail', 'nullable', 'date_format:Y/m']
        ];
    }
}
