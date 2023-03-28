<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plan;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Plan\GrowthSimulation;
use App\Services\Master\FactoryService;
use App\Services\Plan\GrowthSimulationService;
use App\Services\Plan\PlannedArrangementStatusWorkService;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementStatusWorkController extends Controller
{
    /**
     * @var \App\Services\Plan\PlannedArrangementStatusWorkService
     */
    private $planned_arrangement_status_work_service;

    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Plan\GrowthSimulationService
     */
    private $growth_simulation_service;

    /**
     * @param  \App\Services\Plan\PlannedArrangementStatusWorkService $planned_arrangement_status_work_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Plan\GrowthSimulationService $growth_simulation_service
     * @return void
     */
    public function __construct(
        PlannedArrangementStatusWorkService $planned_arrangement_status_work_service,
        FactoryService $factory_service,
        GrowthSimulationService $growth_simulation_service
    ) {
        parent::__construct();

        $this->planned_arrangement_status_work_service = $planned_arrangement_status_work_service;
        $this->factory_service = $factory_service;
        $this->growth_simulation_service = $growth_simulation_service;
    }

    /**
     * 栽培パネル配置図
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse;
     */
    public function index(
        Request $request,
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ) {
        if (! $growth_simulation->canSimulateOnTheDate($simulation_date)) {
            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                $growth_simulation->getFirstPortingDate()->format('Y/m/d'),
                $growth_simulation->getLastHarvestingDate()->format('Y/m/d')
            );

            return redirect()
                ->route('plan.planned_arrangement_status_work.index', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        $display_kubun = $growth_simulation->getDisplayKubun();
        if (! $growth_simulation->hasFixed() && $request->display_kubun) {
            $display_kubun = new DisplayKubun((int)$request->display_kubun);
        }

        $display_kubun_list = $display_kubun->toJsonOptions();

        $planned_cultivation_status_works = $growth_simulation
            ->planned_cultivation_status_works
            ->filterBySimulationDateExceptSeeding($display_kubun, $simulation_date);

        $number_of_beds = $planned_cultivation_status_works->toMapOfStageAndBeds();
        $bed_status_options = $planned_cultivation_status_works->toBedStatusOptions();

        $factory_growing_stages = $growth_simulation->getFactoryGrowingStagesOnTheDate($simulation_date);

        $planned_arrangement_status_works = $this->planned_arrangement_status_work_service
            ->getPlannedArrangementStatusWorksBySimulationDate(
                $growth_simulation,
                $display_kubun,
                $simulation_date
            );

        $factory_layout = $this->factory_service->getFactroyLayoutWithAllocation(
            $growth_simulation->factory_species->factory,
            $display_kubun,
            $factory_growing_stages,
            $planned_arrangement_status_works
        );

        return view('plan.planned_arrangement_status_work.index')->with(compact(
            'growth_simulation',
            'simulation_date',
            'display_kubun_list',
            'number_of_beds',
            'bed_status_options',
            'factory_growing_stages',
            'factory_layout'
        ));
    }

    /**
     * 栽培パネル配置図 帳票
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param \App\ValueObjects\Date\SimulationDate $simulation_date
     */
    public function export(
        Request $request,
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ) {
        $display_kubun = $growth_simulation->getDisplayKubun();
        if (! $growth_simulation->hasFixed() && $request->display_kubun) {
            $display_kubun = new DisplayKubun((int)$request->display_kubun);
        }

        $factory_growing_stages = $growth_simulation->getFactoryGrowingStagesOnTheDate($simulation_date);

        $planned_arrangement_status_works = $this->planned_arrangement_status_work_service
            ->getPlannedArrangementStatusWorksBySimulationDate(
                $growth_simulation,
                $display_kubun,
                $simulation_date
            );

        $factory_layout = $this->factory_service->getFactroyLayoutWithAllocation(
            $growth_simulation->factory_species->factory,
            $display_kubun,
            $factory_growing_stages,
            $planned_arrangement_status_works
        );

        return $this->planned_arrangement_status_work_service->exportPlannedArrangementStatusWork(
            $growth_simulation,
            $simulation_date,
            $factory_growing_stages,
            $factory_layout,
            $request->label_of_bed
        );
    }

    /**
     * 栽培パネル配置図 保存
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(
        Request $request,
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date
    ): RedirectResponse {
        if (! $growth_simulation->canSimulateOnTheDate($simulation_date)) {
            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                $growth_simulation->getFirstPortingDate()->format('Y/m/d'),
                $growth_simulation->getLastHarvestingDate()->format('Y/m/d')
            );

            return redirect()
                ->route('plan.planned_arrangement_status_work.index', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        try {
            if (! $growth_simulation->hasFixed()) {
                $this->planned_arrangement_status_work_service->updatePlannedArrangementStatusWorks(
                    $growth_simulation,
                    $simulation_date,
                    $request->statuses ?: []
                );
            }
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 栽培パネル配置図詳細
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param  int $floor
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function detail(
        Request $request,
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        int $floor
    ) {
        if (! $growth_simulation->canSimulateOnTheDate($simulation_date)) {
            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                $growth_simulation->getFirstPortingDate()->format('Y/m/d'),
                $growth_simulation->getLastHarvestingDate()->format('Y/m/d')
            );

            return redirect()
                ->route('plan.planned_arrangement_status_work.index', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        $display_kubun = $growth_simulation->getDisplayKubun();
        if (! $growth_simulation->hasFixed() && $request->display_kubun) {
            $display_kubun = new DisplayKubun((int)$request->display_kubun);
        }

        $planned_arrangement_detail_status_works = $this->planned_arrangement_status_work_service
            ->getPlannedArrangementDetailStatusWorksBySimulationDate(
                $growth_simulation,
                $display_kubun,
                $simulation_date
            );

        $factory = $growth_simulation->factory_species->factory;
        $factory_layout = $this->factory_service->getFactroyLayoutWithDetailAllocation(
            $factory,
            $display_kubun,
            $growth_simulation->getFactoryGrowingStagesOnTheDate($simulation_date),
            $planned_arrangement_detail_status_works,
            $floor
        );

        return view('plan.planned_arrangement_status_work.detail')->with(compact(
            'growth_simulation',
            'simulation_date',
            'factory',
            'factory_layout'
        ));
    }

    /**
     * 栽培パネル配置図詳細 帳票
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param \App\ValueObjects\Date\SimulationDate $simulation_date
     * @param int $floor
     */
    public function exportDetail(
        Request $request,
        GrowthSimulation $growth_simulation,
        SimulationDate $simulation_date,
        int $floor
    ) {
        $display_kubun = $growth_simulation->getDisplayKubun();
        if (! $growth_simulation->hasFixed() && $request->display_kubun) {
            $display_kubun = new DisplayKubun((int)$request->display_kubun);
        }

        $factory_growing_stages = $growth_simulation->getFactoryGrowingStagesOnTheDate($simulation_date);

        $planned_arrangement_detail_status_works = $this->planned_arrangement_status_work_service
            ->getPlannedArrangementDetailStatusWorksBySimulationDate(
                $growth_simulation,
                $display_kubun,
                $simulation_date
            );

        $factory_layout = $this->factory_service->getFactroyLayoutWithDetailAllocationToExport(
            $growth_simulation->factory_species->factory,
            $display_kubun,
            $factory_growing_stages,
            $planned_arrangement_detail_status_works,
            $floor
        );

        return $this->planned_arrangement_status_work_service->exportPlannedArrangementDetailStatusWork(
            $growth_simulation,
            $simulation_date,
            $factory_growing_stages,
            $factory_layout
        );
    }
}
