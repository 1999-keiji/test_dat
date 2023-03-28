<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\SearchCollectionRequestRequest;
use App\Http\Requests\Shipment\UpdateShipmentDataOfOrdersRequest;
use App\Services\Master\CustomerService;
use App\Services\Master\FactoryService;
use App\Services\Order\OrderService;
use App\Services\Shipment\CollectionRequestLogService;

class CollectionRequestController extends Controller
{
    /**
     * @var \App\Services\Order\OrderService
     */
    private $order_service;

    /**
     * @var \App\Services\Master\FactoryService $factory_service
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\CustomerService
     */
    private $customer_service;

    /**
     * @var \App\Services\Master\CollectionRequestLogService
     */
    private $collection_request_log_service;

    /**
     * @param  \App\Services\Order\OrderService $order_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\CollectionRequestLogService $collection_request_log_service
     * @return void
     */
    public function __construct(
        OrderService $order_service,
        FactoryService $factory_service,
        CustomerService $customer_service,
        CollectionRequestLogService $collection_request_log_service
    ) {
        parent::__construct();

        $this->order_service = $order_service;
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->collection_request_log_service = $collection_request_log_service;
    }

    /**
     * 集荷依頼書 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $grouped_orders = [];

        $params = $request->session()->get('shipment.collection_request.search', []);
        if (count($params) !== 0) {
            $grouped_orders = $this->order_service->searchOrdersToOutputCollectionRequest($params);
        }

        return view('shipment.collection_request.index')->with(compact('params', 'grouped_orders'));
    }

    /**
     * 集荷依頼書 検索
     *
     * @param  \App\Http\Requests\Shipment\SearchCollectionRequestRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchCollectionRequestRequest $request): RedirectResponse
    {
        $request->session()->put('shipment.collection_request.search', $request->all());
        return redirect()->route('shipment.collection_request.index');
    }

    /**
     * 集荷依頼書 出力
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function export(Request $request)
    {
        $order_numbers = [];
        foreach ($request->group_check as $group) {
            $order_numbers = array_merge($order_numbers, explode('-', $group));
        }

        $params = $request->session()->get('shipment.collection_request.search');
        if (count($params) === 0) {
            return redirect()->route('shipment.collection_request.index');
        }

        $factory = $this->factory_service->find($params['factory_code']);
        $customer = $this->customer_service->find($params['customer_code']);

        try {
            $grouped_orders = $this->order_service->searchOrdersToOutputCollectionRequest($params, $order_numbers);
            $this->collection_request_log_service
                ->exportCollectionRequestFiles($order_numbers, $factory, $customer, $grouped_orders);
        } catch (TemplateFileDoesNotExistException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['not_found']]);
        } catch (LaravelExcelException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }
    }

    /**
     * 出荷関連データの更新
     *
     * @param  \App\Http\Requests\Shipment\UpdateShipmentDataOfOrdersRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(UpdateShipmentDataOfOrdersRequest $request): RedirectResponse
    {
        try {
            $this->order_service->updateShipmentDataOfOrders($request->orders);
        } catch (OptimisticLockException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
