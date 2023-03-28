<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\ExportShipmentDataFileRequest;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;
use App\Services\Shipment\ProductAllocationService;
use App\Services\Shipment\ProductizedResultService;
use App\ValueObjects\Enum\ShipmentDataExportFile;

class ShipmentDataExportController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\CustomerService
     */
    private $customer_service;

    /**
     * @var \App\Services\Shipment\ProductizedResultService
     */
    private $productized_result_service;

    /**
     * @var \App\Services\Shipment\ProductAllocationService
     */
    private $product_allocation_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Shipment\ProductizedResultService $productized_result_service
     * @param  \App\Services\Shipment\ProductAllocationService $product_allocation_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service,
        ProductizedResultService $productized_result_service,
        ProductAllocationService $product_allocation_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->productized_result_service = $productized_result_service;
        $this->product_allocation_service = $product_allocation_service;
    }

    /**
     * 出荷データ出力
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('shipment.shipment_data_export.index');
    }

    /**
     * 出荷データ出力 Excel出力
     *
     * @param \App\Http\Requests\Shipment\ExportShipmentDataFileRequest $request
     */
    public function export(ExportShipmentDataFileRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);

        try {
            $shipment_data_export_file = (int)$request->shipment_data_export_file;
            if ($shipment_data_export_file === ShipmentDataExportFile::BY_DAY) {
                $this->productized_result_service->exportProductizedResultsPerHarvestingDate($request->all(), $factory);
            }

            if ($shipment_data_export_file === ShipmentDataExportFile::BY_CUSTOMER) {
                $customer = null;
                if ($request->customer_code) {
                    $customer = $this->customer_service->find($request->customer_code);
                }

                $this->product_allocation_service
                    ->exportOrdersAndProductAllocationsPerCustomer($request->all(), $factory, $customer);
            }
        } catch (LaravelExcelException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }
    }
}
