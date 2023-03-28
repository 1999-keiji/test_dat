<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;
use App\ValueObjects\Enum\OutputFile;
use App\ValueObjects\Enum\PrintState;

class SearchFormOutputRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Models\Master\Customer
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
     * @var \App\ValueObjects\Enum\OutputFile
     */
    private $output_file;

    /**
     * @var \App\ValueObjects\Enum\PrintState
     */
    private $print_state;

    /**
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\ValueObjects\Enum\OutputFile $output_file
     * @param  \App\ValueObjects\Enum\PrintState $print_state
     * @return void
     */
    public function __construct(
        Factory $factory,
        Customer $customer,
        EndUser $end_user,
        DeliveryDestination $delivery_destination,
        OutputFile $output_file,
        PrintState $print_state
    ) {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user = $end_user;
        $this->delivery_destination = $delivery_destination;
        $this->output_file = $output_file;
        $this->print_state = $print_state;
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
            'output_file' => [
                'bail',
                'required',
                Rule::in($this->output_file->all())
            ],
            'print_state' => [
                'bail',
                'required',
                Rule::in($this->print_state->all())
            ],
            'end_user_code' => ['bail', 'nullable', "exists:{$this->end_user->getTable()}"],
            'delivery_destination_code' => [ 'bail', 'nullable', "exists:{$this->delivery_destination->getTable()}"],
            'shipping_date_from' => ['bail', 'nullable', 'date'],
            'shipping_date_to' => ['bail', 'nullable', 'date', 'after_or_equal:shipping_date_from'],
            'delivery_date_from' => ['bail', 'nullable', 'date'],
            'delivery_date_to' => ['bail', 'nullable', 'date', 'after_or_equal:delivery_date_from'],
            'order_number' => ['bail', 'nullable', 'string', 'max:14'],
            'base_plus_order_number' => ['bail', 'nullable', 'string', 'max:10'],
            'base_plus_order_chapter_number' => ['bail', 'nullable', 'string', 'max:3']
        ];
    }
}
