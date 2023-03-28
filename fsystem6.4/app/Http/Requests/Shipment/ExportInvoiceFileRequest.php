<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;
use App\Models\Master\Customer;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;

class ExportInvoiceFileRequest extends FormRequest
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
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\Customer $customer
     * @param \App\Models\Master\EndUser $end_user
     * @return void
     */
    public function __construct(Factory $factory, Customer $customer, EndUser $end_user)
    {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user = $end_user;
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
            'delivery_month' => ['bail', 'required', 'date_format:Y/m']
        ];
    }
}
