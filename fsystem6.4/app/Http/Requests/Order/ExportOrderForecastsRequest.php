<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Species;
use App\ValueObjects\Date\DeliveryDate;

class ExportOrderForecastsRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     * @var \App\Models\Master\Species
     */
    private $species;

    /**
     * @var \App\Models\Master\FactoryProduct
     */
    private $factory_product;

    /**
     * @var \App\Models\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\ValueObjects\Date\DeliveryDate
     */
    private $delivery_date;

    /**
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_date
     * @return void
     */
    public function __construct(
        Factory $factory,
        Species $species,
        FactoryProduct $factory_product,
        DeliveryDestination $delivery_destination,
        DeliveryDate $delivery_date
    ) {
        $this->factory = $factory;
        $this->species = $species;
        $this->factory_product = $factory_product;
        $this->delivery_destination = $delivery_destination;
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
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'species_code' => ['bail', 'required', "exists:{$this->species->getTable()}"],
            'factory_product_sequence_number' => [
                'bail',
                'nullable',
                Rule::exists($this->factory_product->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('factory_code', $this->factory_code);
                })
            ],
            'delivery_destination_code' => ['bail', 'nullable', "exists:{$this->delivery_destination->getTable()}"],
            'delivery_date' => [
                'bail',
                'required',
                "date_format:{$this->delivery_date->getDateFormat()}"
            ],
            'harvesting_date' => ['bail', 'required', 'date'],
            'display_term' => ['bail', 'required', 'in:date'],
            'week_term' => [
                'bail',
                'required',
                "in:{$this->delivery_date->getWeekTermOfExportOrderForecast()}"
            ],
        ];
    }
}
