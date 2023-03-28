<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\SpeciesService;
use App\ValueObjects\String\FactorySpeciesCode;
use App\ValueObjects\Enum\CanDisplay;
use App\ValueObjects\Enum\GrowingStage;

class FactorySpeciesComposer
{
    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @var \App\ValueObjects\Enum\CanDisplay
     */
    private $can_display;

    /**
     * @param \App\Services\Master\SpeciesService $species_service
     * @param  \App\ValueObjects\Enum\CanDisplay $can_display
     * @return void
     */
    public function __construct(
        SpeciesService $species_service,
        CanDisplay $can_display
    ) {
        $this->species_service = $species_service;
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
            'species' => $this->species_service->getAllSpecies(),
            'factory_species_code' => new FactorySpeciesCode(),
            'can_display_list' => $this->can_display->all(),
            'growing_stage' => new GrowingStage()
        ]);
    }
}
