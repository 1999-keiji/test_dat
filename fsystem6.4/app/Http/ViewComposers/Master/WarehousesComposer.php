<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\String\WarehouseCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;
use App\ValueObjects\Integer\DeliveryLeadTime;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\CanDisplay;
use App\ValueObjects\Enum\ShipmentLeadTime;

class WarehousesComposer
{
    /**
     * @var \App\ValueObjects\Enum\CanDisplay
     */
    private $can_display;

    /**
     * @param  \App\ValueObjects\Enum\CanDisplay $can_display
     * @return void
     */
    public function __construct(CanDisplay $can_display)
    {
        $this->can_display = $can_display;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $view->with([
            'warehouse_code' => new WarehouseCode(),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode(),
            'can_display_list' => $this->can_display->all(),
            'delivery_lead_time' => new DeliveryLeadTime(),
            'shipment_lead_time' => new ShipmentLeadTime()
        ]);
    }
}
