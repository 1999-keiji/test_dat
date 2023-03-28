<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\ValueObjects\Date\DeliveryDate;

class GetShippingDateRequest extends FormRequest
{
    /**
     * @var \App\Model\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Model\Master\Factory
     */
    private $factory;

    /**
     * @var \App\ValueObjects\Date\DeliveryDate
     */
    private $delivery_date;

    /**
     * @param  \App\Model\Master\DeliveryDestination $delivery_destination
     * @param  \App\Model\Master\Factory $factory
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_date
     * @return void
     */
    public function __construct(
        DeliveryDestination $delivery_destination,
        Factory $factory,
        DeliveryDate $delivery_date
    ) {
        $this->delivery_destination = $delivery_destination;
        $this->factory = $factory;
        $this->delivery_date = $delivery_date;
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
            'delivery_destination_code' => [
                'bail',
                'required',
                "exists:{$this->delivery_destination->getTable()}"
            ],
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'delivery_date' => [
                'bail',
                'required',
                "date_format:{$this->delivery_date->getDateFormat()}"
            ]
        ];
    }
}
