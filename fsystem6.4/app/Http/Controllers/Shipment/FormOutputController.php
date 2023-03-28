<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\DisabledToApplyTaxException;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\SearchFormOutputRequest;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;
use App\Services\Order\OrderService;
use App\Services\Shipment\InvoiceReceiptInfomationLogService;
use App\Services\Shipment\ShipmentInfomationLogService;
use App\ValueObjects\Enum\OutputFile;

class FormOutputController extends Controller
{
    /**
     * @var \App\Services\Shipment\OrderService
     */
    private $order_service;

    /**
     * @var \App\Services\Shipment\ShipmentInfomationLogService
     */
    private $shipment_infomation_log_service;

    /**
     * @var \App\Services\Shipment\InvoiceReceiptInfomationLogService
     */
    private $invoice_receipt_infomation_log_service;

    /**
     * @var \App\Services\Master\FactoryService $factory_service
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\CustomerService $customer_service
     */
    private $customer_service;

    /**
     * @param  \App\Services\Shipment\OrderService $order_service
     * @param  \App\Services\Shipment\ShipmentInfomationLogService $shipment_infomation_log_service
     * @param  \App\Services\Shipment\InvoiceReceiptInfomationLogService $invoice_receipt_infomation_log_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @return void
     */
    public function __construct(
        OrderService $order_service,
        ShipmentInfomationLogService $shipment_infomation_log_service,
        InvoiceReceiptInfomationLogService $invoice_receipt_infomation_log_service,
        FactoryService $factory_service,
        CustomerService $customer_service
    ) {
        parent::__construct();

        $this->order_service = $order_service;
        $this->shipment_infomation_log_service = $shipment_infomation_log_service;
        $this->invoice_receipt_infomation_log_service = $invoice_receipt_infomation_log_service;
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
    }

    /**
     * 出荷作業帳票出力 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $grouped_orders = [];

        $params = $request->session()->get('shipment.form_output.search', []);
        if (count($params) !== 0) {
            $grouped_orders = $this->order_service->searchOrdersToOutputShipmentFiles($params);
        }

        return view('shipment.form_output.index')->with(compact('params', 'grouped_orders'));
    }

    /**
     * 出荷作業帳票出力 検索
     *
     * @param  \App\Http\Requests\Shipment\SearchFormOutputRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchFormOutputRequest $request): RedirectResponse
    {
        $request->session()->put('shipment.form_output.search', $request->all());
        return redirect()->route('shipment.form_output.index');
    }

    /**
     * 出荷作業帳票出力 PDFダウンロード
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function download(Request $request)
    {
        $order_numbers = [];
        foreach ($request->group_check as $group) {
            $order_numbers = array_merge($order_numbers, explode('-', $group));
        }

        $params = $request->session()->get('shipment.form_output.search', []);
        if (count($params) === 0) {
            return redirect()->route('shipment.form_output.index');
        }

        $factory = $this->factory_service->find($params['factory_code']);
        $customer = $this->customer_service->find($params['customer_code']);

        try {
            $grouped_orders = $this->order_service->getOrdersToOutputShipmentFiles($params, $customer, $order_numbers);
        } catch (DisabledToApplyTaxException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['tax_error']]);
        }

        if ($request->output_file == OutputFile::SHIPPING_INFO) {
            try {
                $file = $this->shipment_infomation_log_service->exportShipmentFiles(
                    $order_numbers,
                    $factory,
                    $customer,
                    $grouped_orders
                );
            } catch (TemplateFileDoesNotExistException $e) {
                report($e);
                return redirect()->back()->withInput()->with(['alert' => $this->operations['not_found']]);
            } catch (PDOException $e) {
                report($e);
                return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
            }

            return response()->exportPdf($file['file']->Output(null, 'S'), $file['name']);
        }

        if ($request->output_file == OutputFile::NOTE_RECEIPT) {
            try {
                $file = $this->invoice_receipt_infomation_log_service->exportReceiptFiles(
                    $order_numbers,
                    $factory,
                    $customer,
                    $grouped_orders
                );
            } catch (TemplateFileDoesNotExistException $e) {
                report($e);
                return redirect()->back()->withInput()->with(['alert' => $this->operations['not_found']]);
            } catch (PDOException $e) {
                report($e);
                return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
            }

            return response()->exportPdf($file['file']->Output(null, 'S'), $file['name']);
        }

        return redirect()->route('shipment.form_output.index');
    }
}
