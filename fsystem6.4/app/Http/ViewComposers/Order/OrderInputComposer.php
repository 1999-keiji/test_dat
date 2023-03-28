<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Order;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\Services\Master\CustomerService;
use App\Services\Master\CurrencyService;
use App\ValueObjects\String\CurrencyCode;

class OrderInputComposer
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
     * @var \App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass
     */
    private $statement_delivery_price_display_class;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\CurrencyService $currency_service
     * @param  \App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service,
        CurrencyService $currency_service
    ) {
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->currency_service = $currency_service;
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
            'default_currency_code' => CurrencyCode::getDefaultCurrencyCode()
        ]);
    }
}
