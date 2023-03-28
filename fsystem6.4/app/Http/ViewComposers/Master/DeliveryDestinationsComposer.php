<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\CurrencyService;
use App\Services\Master\FactoryService;
use App\Services\Master\TransportCompanyService;
use App\ValueObjects\Enum\CanDisplay;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\DeliveryDestinationClass;
use App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\ShipmentWayClass;
use App\ValueObjects\Enum\StatementOfDeliveryOutputClass;
use App\ValueObjects\Enum\StatementOfShipmentOutputClass;
use App\ValueObjects\Enum\ShipmentLeadTime;
use App\ValueObjects\Decimal\UnitPrice;
use App\ValueObjects\Integer\DeliveryLeadTime;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\DeliveryDestinationCode;
use App\ValueObjects\String\PostalCode;

class DeliveryDestinationsComposer
{
     /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\CurrencyService
     */
    private $currency_service;

    /**
     * @var \App\Services\Master\TransportCompanyService
     */
    private $transport_company_service;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryOutputClass
     */
    private $statement_of_delivery_output_class;

    /**
     * @var \App\ValueObjects\Enum\ShipmentWayClass
     */
    private $shipment_way_class;

    /**
     * @var \App\ValueObjects\Enum\DeliveryDestinationClass
     */
    private $delivery_destination_class;

    /**
     * @var \App\ValueObjects\Enum\CanDisplay
     */
    private $can_display;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CurrencyService $currency_service
     * @param  \App\Services\Master\TransportCompanyService $transport_company_service
     * @param  \App\ValueObjects\Enum\DeliveryDestinationClass $delivery_destination_class
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryOutputClass $statement_of_delivery_output_class
     * @param  \App\ValueObjects\Enum\ShipmentWayClass $shipment_way_class
     * @param  \App\ValueObjects\Enum\CanDisplay $can_display
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        CurrencyService $currency_service,
        TransportCompanyService $transport_company_service,
        StatementOfDeliveryOutputClass $statement_of_delivery_output_class,
        ShipmentWayClass $shipment_way_class,
        DeliveryDestinationClass $delivery_destination_class,
        CanDisplay $can_display
    ) {
        $this->currency_service = $currency_service;
        $this->factory_service = $factory_service;
        $this->transport_company_service = $transport_company_service;
        $this->statement_of_delivery_output_class = $statement_of_delivery_output_class;
        $this->shipment_way_class = $shipment_way_class;
        $this->delivery_destination_class = $delivery_destination_class;
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
            'delivery_destination_code' => new DeliveryDestinationCode(),
            'creating_type' => new CreatingType(CreatingType::MANUAL_CREATED),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode(),
            'statement_of_delivery_output_class_list' => $this->statement_of_delivery_output_class->all(),
            'shipment_way_class_list' => $this->shipment_way_class->all(),
            'delivery_destination_class_list' => $this->delivery_destination_class->all(),
            'can_display_list' => $this->can_display->all(),
            'can_display' => new CanDisplay(),
            'delivery_lead_time' => new DeliveryLeadTime(),
            'shipment_lead_time' => new ShipmentLeadTime(),
            'factories' => $this->factory_service->getAllFactories(),
            'currencies' => $this->currency_service->getAllCurrencies(),
            'transport_companies' => $this->transport_company_service->getAllTransportCompanies(),
            'fsystem_statement_of_delivery_output_class' => new FsystemStatementOfDeliveryOutputClass(),
            'statement_of_shipment_output_class' => new StatementOfShipmentOutputClass(),
            'unit_price' => new UnitPrice()
        ]);
    }
}
