<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PurchaseOrderExcelImportRequest;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;
use App\Services\Order\PurchaseOrderExcelImportService;

class PurchaseOrderExcelImportController extends Controller
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
     * @var \App\Services\Order\PurchaseOrderExcelImportService
     */
    private $purchase_order_excel_import_service;

    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service,
        PurchaseOrderExcelImportService $purchase_order_excel_import_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->purchase_order_excel_import_service = $purchase_order_excel_import_service;
    }

    /**
     * 注文書Excel取込
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $factories = $this->factory_service->getAllFactories();
        $customers = $this->customer_service->getAllCustomers();

        return view('order.purchase_order_excel_import.index')->with(compact('factories', 'customers'));
    }

    /**
     * 注文書Excel取込
     *
     * @param  \App\Http\Requests\Order\PurchaseOrderExcelImportRequest $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function import(PurchaseOrderExcelImportRequest $request): RedirectResponse
    {
        if (! $this->purchase_order_excel_import_service->checkUploadedFile($request->all())) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['not_matched_file']]);
        }

        $factory = $this->factory_service->find($request->factory_code);
        $customer = $this->customer_service->find($request->customer_code);
        [$orders, $errors] = $this->purchase_order_excel_import_service
            ->parseUploadedFile($request->all(), $factory, $customer);

        if (count($errors) !== 0) {
            return redirect()->back()->withInput()->with([
                'alert' => $this->operations['invalid'],
                'error_messages' => $errors
            ]);
        }

        try {
            $this->purchase_order_excel_import_service->importUploadedOrders($orders, $factory);

            $alert = $this->operations['success'];
            $alert['message'] = sprintf($alert['message'], count($orders));

            return redirect()->back()->with(compact('alert'));
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }
    }
}
