<?php

namespace App\Http\Controllers\Plan;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\ExportGrowthPlannedTableRequest;
use App\Services\Master\FactoryService;
use App\Services\Plan\PanelStateService;

class GrowthPlannedTableController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Plan\PanelStateService
     */
    private $panel_state_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Plan\PanelStateService $panel_state_service
     * @return void
     */
    public function __construct(FactoryService $factory_service, PanelStateService $panel_state_service)
    {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->panel_state_service = $panel_state_service;
    }

    /**
     * 生産計画表
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('plan.growth_planned_table.index');
    }

    /**
     * 生産計画表 出力
     *
     * @param \App\Http\Requests\Plan\ExportGrowthPlannedTableRequest
     */
    public function export(ExportGrowthPlannedTableRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $this->panel_state_service->exportGrowthPlannedTable($factory, $request->all());
    }
}
