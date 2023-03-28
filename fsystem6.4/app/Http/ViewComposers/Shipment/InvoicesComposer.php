<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Shipment;

use Illuminate\View\View;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;

class InvoicesComposer
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
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service
    ) {
        $this->factory_service  = $factory_service;
        $this->customer_service = $customer_service;
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
            'customers' => $this->customer_service->getAllCustomers()
        ]);
    }
}
