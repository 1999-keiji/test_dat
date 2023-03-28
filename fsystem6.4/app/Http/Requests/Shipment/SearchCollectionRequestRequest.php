<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\CollectionTime;
use App\Models\Master\Customer;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;
use App\Models\Master\TransportCompany;

class SearchCollectionRequestRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\Factory
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
     * @var \App\Model\Master\TransportCompany
     */
    private $transport_company;

    /**
     * @var \App\Model\Master\CollectionTime
     */
    private $collection_time;

    /**
     * @param  \App\Model\Master\Factory $factory
     * @param  \App\Model\Master\Customer $customer
     * @param  \App\Model\Master\EndUser $end_user
     * @param  \App\Model\Master\TransportCompany $transport_company
     * @param  \App\Model\Master\CollectionTime $collection_time
     * @return void
     */
    public function __construct(
        Factory $factory,
        Customer $customer,
        EndUser $end_user,
        TransportCompany $transport_company,
        CollectionTime $collection_time
    ) {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user = $end_user;
        $this->transport_company = $transport_company;
        $this->collection_time = $collection_time;
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
            'factory_code' => [
                'bail',
                'required',
                Rule::exists($this->factory->getTable())
            ],
            'customer_code' => [
                'bail',
                'required',
                Rule::exists($this->customer->getTable())
            ],
            'end_user_code' => [
                'bail',
                'nullable',
                Rule::exists($this->end_user->getTable())
            ],
            'shipping_date' => ['bail', 'nullable', 'date_format:Y/m/d'],
            'transport_company_code' => [
                'bail',
                'nullable',
                Rule::exists($this->transport_company->getTable())
            ],
            'collection_time_sequence_number' => [
                'bail',
                'nullable',
                Rule::exists($this->collection_time->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('transport_company_code', $this->transport_company_code);
                })
            ]
        ];
    }
}
