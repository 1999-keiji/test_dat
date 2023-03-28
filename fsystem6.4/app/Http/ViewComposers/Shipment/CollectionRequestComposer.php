<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Shipment;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\Services\Master\CustomerService;
use App\Services\Master\TransportCompanyService;

class CollectionRequestComposer
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
     * @var \App\Services\Master\TransportCompanyService
     */
    private $transport_company_service;

    /**
     * @param \App\Services\Master\FactoryService $factory_service
     * @param \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\TransportCompanyService $transport_company_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service,
        TransportCompanyService $transport_company_service
    ) {
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
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
            'transport_companies' => $this->transport_company_service->getAllTransportCompanies()
        ]);
    }
}
