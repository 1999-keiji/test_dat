<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\String\DeliveryDestinationCode;
use App\ValueObjects\String\WarehouseCode;
use App\ValueObjects\Integer\DeliveryLeadTime;
use App\ValueObjects\Enum\ShipmentLeadTime;

class DeliveryWarehousesComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $view->with([
            'delivery_destination_code' => new DeliveryDestinationCode(),
            'warehouse_code' => new WarehouseCode(),
            'delivery_lead_time' => new DeliveryLeadTime(),
            'shipment_lead_time' => new ShipmentLeadTime()
        ]);
    }
}
