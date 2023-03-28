<?php

namespace App\Http\Controllers\Plan;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\SaveFloorCultivationStocksRequest;
use App\Models\Plan\GrowthSimulation;
use App\Services\Plan\PlannedCultivationStatusWorkService;
use App\ValueObjects\Date\SimulationDate;

class PlannedCultivationStatusWorkController extends Controller
{
    /**
     * @var \App\Services\Plan\PlannedCultivationStatusWorkService
     */
    private $planned_cultivation_status_work_service;

    /**
     * @return void
     */
    public function __construct(PlannedCultivationStatusWorkService $planned_cultivation_status_work_service)
    {
        parent::__construct();

        $this->planned_cultivation_status_work_service = $planned_cultivation_status_work_service;
    }

    /**
     * 各階栽培株数 一覧
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, GrowthSimulation $growth_simulation, SimulationDate $simulation_date)
    {
        if (! $growth_simulation->canSimulateOnTheDate($simulation_date)) {
            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                $growth_simulation->getFirstPortingDate()->format('Y/m/d'),
                $growth_simulation->getLastHarvestingDate()->format('Y/m/d')
            );

            return redirect()
                ->route('plan.planned_cultivation_status_work.index', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        $planned_cultivation_status_works = $this->planned_cultivation_status_work_service
            ->getPlannedCultivationStatusWorks($growth_simulation, $simulation_date);
        $simulatable_dates = $simulation_date
            ->getSimulatableDatesOnTheWeek($growth_simulation->factory_species->factory);

        return view('plan.planned_cultivation_status_work.index')
            ->with(compact(
                'growth_simulation',
                'simulation_date',
                'planned_cultivation_status_works',
                'simulatable_dates'
            ));
    }

    /**
     * 各階栽培株数 一覧 保存
     *
     * @param  \App\Http\Requests\Plan\SaveFloorCultivationStocksRequest $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(
        SaveFloorCultivationStocksRequest $request,
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
                ->route('plan.planned_cultivation_status_work.index', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        try {
            if (! $growth_simulation->hasFixed()) {
                $this->planned_cultivation_status_work_service
                    ->updatePlannedCultivationStatusWorks($growth_simulation, $simulation_date, $request->all());
            }
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 各階栽培株数 合計表 一覧
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function sum(Request $request, GrowthSimulation $growth_simulation, SimulationDate $simulation_date)
    {
        if (! $growth_simulation->canSimulateOnTheDate($simulation_date)) {
            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                $growth_simulation->getFirstPortingDate()->format('Y/m/d'),
                $growth_simulation->getLastHarvestingDate()->format('Y/m/d')
            );

            return redirect()
                ->route('plan.planned_cultivation_status_work.sum', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        $planned_cultivation_status_works = $this->planned_cultivation_status_work_service
            ->getPlannedCultivationStatusWorks($growth_simulation, $simulation_date);
        $simulatable_dates = $simulation_date
            ->getSimulatableDatesOnTheWeek($growth_simulation->factory_species->factory);

        return view('plan.planned_cultivation_status_work.sum')->with(compact(
            'growth_simulation',
            'simulation_date',
            'planned_cultivation_status_works',
            'simulatable_dates'
        ));
    }

    /**
     * 各階栽培株数 帳票出力
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @param  \App\ValueObjects\Date\SimulationDate $simulation_date
     */
    public function export(Request $request, GrowthSimulation $growth_simulation, SimulationDate $simulation_date)
    {
        if (! $growth_simulation->canSimulateOnTheDate($simulation_date)) {
            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                $growth_simulation->getFirstPortingDate()->format('Y/m/d'),
                $growth_simulation->getLastHarvestingDate()->format('Y/m/d')
            );

            return redirect()
                ->route('plan.planned_cultivation_status_work.sum', [
                    $growth_simulation->getJoinedPrimaryKeys(),
                    $growth_simulation->getFirstPortingDate()->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        $this->planned_cultivation_status_work_service
            ->exportPlannedCultivationStatusWorks($growth_simulation, $simulation_date);
    }
}
