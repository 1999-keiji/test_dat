<?php

declare(strict_types=1);

namespace App\Http\Controllers\FactoryProductionWork;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\FactoryProductionWork\ExportWorkInstructionRequest;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Services\FactoryProductionWork\WorkInstructionService;
use App\Services\Master\FactoryService;
use App\Services\Master\SpeciesService;
use App\ValueObjects\Date\WorkingDate;

class WorkInstructionController extends Controller
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
     * @var \App\Services\FactoryProductionWork\WorkInstructionService
     */
    private $work_instruction_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\SpeciesService $species_service
     * @param  \App\Services\FactoryProductionWork\WorkInstructionService $work_instruction_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        SpeciesService $species_service,
        WorkInstructionService $work_instruction_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->species_service = $species_service;
        $this->work_instruction_service = $work_instruction_service;
    }

    /**
     * 作業指示書 画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('factory_production_work.work_instruction.index');
    }

    /**
     * 作業指示書出力
     *
     * @param \App\Http\Requests\FactoryProductionWork\ExportWorkInstructionRequest $request
     */
    public function export(ExportWorkInstructionRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $species = $this->species_service->find($request->species_code);

        $working_date_term = [
            'from' => WorkingDate::parse($request->working_date_from),
            'to' => WorkingDate::parse($request->working_date_to)
        ];

        $factory_species_list = $this->work_instruction_service
            ->getFactorySpeciesListWithWorkingDates($factory, $species, $working_date_term);

        $working_dates = $this->work_instruction_service
            ->getWorkingDatesWithSpecies($factory, $species, $working_date_term);

        $this->work_instruction_service
            ->exportWorkInstruction($factory, $species, $factory_species_list, $working_dates);
    }
}
