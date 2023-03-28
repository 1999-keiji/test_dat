<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\SpeciesService;
use App\Services\Master\CurrencyService;
use App\ValueObjects\Decimal\UnitPrice;
use App\ValueObjects\Decimal\Cost;

class FactoryProductsComposer
{
    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @var \App\Services\Master\CurrencyService
     */
    private $currency_service;

    /**
     * @param  \App\Services\Master\SpeciesService $species_service
     * @param  \App\Services\Master\CurrencyService $currency_service
     * @return void
     */
    public function __construct(SpeciesService $species_service, CurrencyService $currency_service)
    {
        $this->species_service = $species_service;
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
            'species_list' => $this->species_service->getAllSpecies(),
            'currencies' => $this->currency_service->getAllCurrencies(),
            'input_group_list' => config('constant.master.factory_products.input_group'),
            'unit_list' => config('constant.master.factory_products.unit'),
            'unit_price' => new UnitPrice(),
            'cost' => new Cost()
        ]);
    }
}
