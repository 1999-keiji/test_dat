<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\DisabledToApplyTaxException;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\ExportInvoiceFileRequest;
use App\Http\Requests\Shipment\FixInvoiceRequest;
use App\Http\Requests\Shipment\SearchInvoicesRequest;
use App\Models\Shipment\Invoice;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;
use App\Services\Order\OrderService;
use App\Services\Shipment\InvoiceService;
use App\ValueObjects\Date\DeliveryDate;

class InvoicesController extends Controller
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
     * @var \App\Services\Order\OrderService
     */
    private $order_service;

    /**
     * @var \App\Services\Shipment\InvoiceService
     */
    private $invoice_service;

    /**
     * @param \App\Services\Master\FactoryService $factory_service
     * @param \App\Services\Master\CustomerService $customer_service
     * @param \App\Services\Order\OrderService $order_service
     * @param \App\Services\Shipment\InvoiceService $invoice_service
     */
    public function __construct(
        FactoryService $factory_service,
        CustomerService $customer_service,
        OrderService $order_service,
        InvoiceService $invoice_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->order_service = $order_service;
        $this->invoice_service = $invoice_service;
    }

    /**
     * 請求書締め
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|Illuminate\Http\RedirectResponse
     */
    public function index(Request $request): View
    {
        $invoices = [];

        $params = $request->session()->get('shipment.invoices.search', []);
        if (count($params) !== 0) {
            $customer = $this->customer_service->find($params['customer_code']);

            $order = $request->only(['sort', 'order']);
            $invoices = $this->invoice_service->searchInvoices($params, $customer, $order);
        }

        return view('shipment.invoices.index')->with(compact('invoices', 'params'));
    }

    /**
     * 請求書締め 検索
     *
     * @param  \App\Http\Requests\Shipment\SearchInvoicesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchInvoicesRequest $request): RedirectResponse
    {
        $request->session()->put('shipment.invoices.search', $request->all());
        return redirect()->route('shipment.invoices.index');
    }

    /**
     * 請求書締め 確定
     *
     * @param  \App\Http\Requests\Shipment\FixInvoiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fix(FixInvoiceRequest $request): RedirectResponse
    {
        try {
            $customer = $this->customer_service->find($request->customer_code);
            $this->invoice_service->fixInvoice($request->all(), $customer);
        } catch (OptimisticLockException | PDOExcption $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('shipment.invoices.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 請求書締め 確定解除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Shipment\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoice_service->cancelInvoice($invoice);
        } catch (OptimisticLockException | PDOExcption $e) {
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('shipment.invoices.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 請求書出力
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function indexExport(Request $request): View
    {
        return view('shipment.invoices.export');
    }

    /**
     * 請求書出力 ファイル出力
     *
     * @param  \App\Http\Requests\Shipment\ExportInvoiceFileRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function export(ExportInvoiceFileRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $customer = $this->customer_service->find($request->customer_code);
        $delivery_month = DeliveryDate::createFromYearMonth($request->delivery_month);

        try {
            $invoice = $this->invoice_service->getFixedInvoice($request->all(), $delivery_month);
            $orders = $this->order_service
                ->getOrdersThatWillOutputOnInvoice($request->all(), $customer, $delivery_month, $invoice);
            if ($orders->isEmpty()) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['order_not_exist']]);
            }
        } catch (DisabledToApplyTaxException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['tax_error']]);
        }

        try {
            $file = $this->invoice_service->createInvoiceFile($factory, $customer, $delivery_month, $invoice, $orders);
        } catch (TemplateFileDoesNotExistException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['not_found']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return response()->exportPdf($file['file']->Output(null, 'S'), $file['name']);
    }
}
