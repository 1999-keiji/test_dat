<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plan;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\ExportGrowthSaleManagementRequest;
use App\Services\Master\FactoryService;
use App\Services\Master\SpeciesService;
use App\Services\Plan\GrowthSaleManagementService;
use App\ValueObjects\Date\HarvestingDate;

class GrowthSaleManagementController extends Controller
{
    /**
     * @var \App\Services\Plan\GrowthSaleManagementService
     */
    private $growth_sale_management_service;

    /**
     * @var \App\Services\Master\FactoryService $factory_service
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\SpeciesService $species_service
     */
    private $species_service;

    /**
     * @param  \App\Services\Plan\ForecastExcelImportService $forecast_excel_import_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\SpeciesService $species_service
     * @return void
     */
    public function __construct(
        GrowthSaleManagementService $growth_sale_management_service,
        FactoryService $factory_service,
        SpeciesService $species_service
    ) {
        parent::__construct();

        $this->growth_sale_management_service = $growth_sale_management_service;
        $this->factory_service = $factory_service;
        $this->species_service = $species_service;
    }

    /**
     * 生産・販売管理表 画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('plan.growth_sale_management.index');
    }

    /**
     * 生産・販売管理表 Excel出力
     *
     * @param \App\Http\Requests\Plan\ExportGrowthSaleManagementRequest $request
     */
    public function export(ExportGrowthSaleManagementRequest $request)
    {
        ini_set('max_execution_time', '600');

        $factory = $this->factory_service->find($request->factory_code);
        $species = $this->species_service->find($request->species_code);
        $harvesting_date = HarvestingDate::parse($request->harvesting_date)->startOfWeek();
        $excel_params = $this->growth_sale_management_service
            ->createGrowthSaleManagementParam($factory, $species, $harvesting_date);

        if (is_null($excel_params)) {
            return redirect()
                ->route('plan.growth_sale_management.index')
                ->withInput()
                ->with(['alert' => $this->operations['not_exist']]);
        }

        return $this->growth_sale_management_service
            ->createForecastExcel($factory, $species, $harvesting_date, $excel_params);
    }

    /**
     * 生産・販売管理表 Excel取込
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request): RedirectResponse
    {
        if (! $this->growth_sale_management_service->checkUploadedFile($request->all())) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['not_matched_file']]);
        }
        try {
            [$forecasted_product_rates, $crops] = $this->growth_sale_management_service
                ->parseUploadedFile($request->all());

            if (count($forecasted_product_rates) === 0 && count($crops) === 0) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['import_data_not_exsit']]);
            }

            $messages = $this->growth_sale_management_service->importUploadedData($forecasted_product_rates, $crops);
            if (count($messages) !== 0) {
                return redirect()->route('plan.growth_sale_management.index')->with([
                    'alert' => $this->operations['success'],
                    'messages' => $messages
                ]);
            }
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('plan.growth_sale_management.index')->with(['alert' => $this->operations['success']]);
    }
}
