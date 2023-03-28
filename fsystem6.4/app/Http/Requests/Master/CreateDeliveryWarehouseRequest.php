<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Warehouse;
use App\ValueObjects\Integer\DeliveryLeadTime;
use App\ValueObjects\Enum\ShipmentLeadTime;

class CreateDeliveryWarehouseRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Models\Master\Warehouse
     */
    private $warehouse;

    /**
     * @var App\ValueObjects\Integer\DeliveryLeadTime
     */
    private $delivery_lead_time;

    /**
     * @var App\ValueObjects\Enum\ShipmentLeadTime
     */
    private $shipment_lead_time;

    /**
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\Warehouse $warehouse
     * @param  \App\ValueObjects\Integer\DeliveryLeadTime $delivery_lead_time
     * @param  \App\ValueObjects\Enum\ShipmentLeadTime $shipment_lead_time
     * @return void
     */
    public function __construct(
        DeliveryDestination $delivery_destination,
        Warehouse $warehouse,
        DeliveryLeadTime $delivery_lead_time,
        ShipmentLeadTime $shipment_lead_time
    ) {
        $this->delivery_destination = $delivery_destination;
        $this->warehouse = $warehouse;
        $this->delivery_lead_time = $delivery_lead_time;
        $this->shipment_lead_time = $shipment_lead_time;
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
            'warehouse_code' => [
                'bail',
                'required',
                "exists:{$this->warehouse->getTable()}"
            ],
            'delivery_lead_time' => [
                'bail',
                'required',
                'integer',
                "min:{$this->delivery_lead_time->getMinimumNum()}",
                "max:{$this->delivery_lead_time->getMaximumNum()}"
            ],
            'shipment_lead_time' => [
                'bail',
                'required',
                Rule::in($this->shipment_lead_time->all())
            ]
        ];
    }
}
