<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Order;

use Illuminate\View\View;
use App\Services\Master\CurrencyService;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;
use App\Services\Master\TransportCompanyService;
use App\ValueObjects\Enum\AllocationStatus;
use App\ValueObjects\Enum\ShipmentStatus;
use App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass;
use App\ValueObjects\Enum\BasisForRecordingSalesClass;
use App\ValueObjects\Enum\SmallPeaceOfPeperTypeCode;
use App\ValueObjects\String\CurrencyCode;

class OrderListComposer
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\CustomerService
     */
    private $customer_service;

    /**
     * @var \App\Services\Master\CurrencyService
     */
    private $currency_service;

    /**
     * @var \App\Services\Master\TransportCompanyService
     */
    private $transport_company_service;

    /**
     * @param  \App\Services\Master\FactoryService $transport_companies_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\CurrencyService $currency_service
     * @param  \App\Services\Master\TransportCompanyService $transport_company_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service,
        CurrencyService $currency_service,
        TransportCompanyService $transport_company_service
    ) {
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->currency_service = $currency_service;
        $this->transport_company_service = $transport_company_service;
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
            'customers' => $this->customer_service->getAllCustomers(),
            'currencies' => $this->currency_service->getAllCurrencies(),
            'default_currency_code' => CurrencyCode::getDefaultCurrencyCode(),
            'transport_companies' => $this->transport_company_service->getAllTransportCompanies(),
            'allocation_status' => new AllocationStatus(),
            'shipment_status' => new ShipmentStatus(),
            'statement_delivery_price_display_class' => new StatementDeliveryPriceDisplayClass(),
            'basis_for_recording_sales_class' => new BasisForRecordingSalesClass(),
            'small_peace_of_peper_type_code' => new SmallPeaceOfPeperTypeCode()
        ]);
    }
}
