<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Order;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Integer\DeliveryLeadTime;
use App\ValueObjects\Enum\ShipmentLeadTime;

class OrderForecastsComposer
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @param \App\Services\Master\FactoryService $factory_service
     * @return void
     */
    public function __construct(FactoryService $factory_service)
    {
        $this->factory_service = $factory_service;
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
            'factories' => $this->factory_service->getAllFactories(),
            'delivery_date' => new DeliveryDate(),
            'delivery_lead_time' => new DeliveryLeadTime(),
            'shipment_lead_time' => new ShipmentLeadTime()
        ]);
    }
}
