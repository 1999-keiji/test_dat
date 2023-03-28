<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plan;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\ExportFacilityStatusListRequest;
use App\Services\Master\FactoryService;
use App\Services\Plan\FacilityStatusListService;
use App\ValueObjects\Date\WorkingDate;

class FacilityStatusListController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Plan\FacilityStatusListService
     */
    private $facility_status_list_service;

    /**
     * @param  \\App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Plan\FacilityStatusListService $facility_status_list_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        FacilityStatusListService $facility_status_list_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->facility_status_list_service = $facility_status_list_service;
    }

    /**
     * 施設利用状況一覧
     *
     * @param  \Illuminate\Http\Request
     */
    public function index(Request $request)
    {
        return view('plan.facility_status_list.index');
    }

    /**
     * 施設利用状況一覧 Excel出力
     *
     * @param \App\Http\Requests\Plan\ExportFacilityStatusListRequest
     */
    public function export(ExportFacilityStatusListRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        if ($factory->factory_species->isEmpty()) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['not_found_factory_species']]);
        }

        try {
            $this->facility_status_list_service
                ->exportFacilityStatusList($factory, WorkingDate::parse($request->working_date));
        } catch (LaravelExcelException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }
    }
}
