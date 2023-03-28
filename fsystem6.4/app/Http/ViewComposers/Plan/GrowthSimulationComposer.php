<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Plan;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;
use App\ValueObjects\Enum\GrowingStage;
use App\ValueObjects\Enum\InputChange;

class GrowthSimulationComposer
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param \App\Services\Master\FactoryService $factory_service
     * @param \App\Services\Plan\GrowthSimulationService $growth_simulation_service
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
            'default_harvesting_date' => (new HarvestingDate())->startOfWeek()->format('Y/m/d'),
            'simulation_date' => new SimulationDate(),
            'growing_stage_list' => (new GrowingStage())->all(),
            'input_change_list' => (new InputChange())->all(),
            'display_kubun_list' => (new DisplayKubun())->all()
        ]);
    }
}
