<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Plan;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\Services\Master\SpeciesService;
use App\ValueObjects\Date\HarvestingDate;

class GrowthSaleManagementComposer
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
     * @param \App\Services\Master\SpeciesService $species_service
     * @return void
     */
    public function __construct(FactoryService $factory_service, SpeciesService $species_service)
    {
        $this->factory_service = $factory_service;
        $this->species_service = $species_service;
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
            'species_list' => $this->species_service->getAllSpecies(),
            'default_harvesting_date' => (new HarvestingDate())->startOfWeek()->format('Y/m/d'),
            'input_group_list' => config('constant.master.factory_products.input_group')
        ]);
    }
}
