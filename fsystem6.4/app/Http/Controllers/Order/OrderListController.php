<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;
use App\Exceptions\DisabledToLinkOrderException ;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\SaveSlipRequest;
use App\Http\Requests\Order\SearchOrdersRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order\Order;
use App\Models\Order\Collections\OrderCollection;
use App\Services\Master\CustomerService;
use App\Services\Master\DeliveryDestinationService;
use App\Services\Master\FactoryService;
use App\Services\Master\FactoryProductService;
use App\Services\Order\OrderListService;
use App\Services\Order\VVFBackboneImportService;

class OrderListController extends Controller
{
    /**
     * @var \App\Services\Order\OrderListService
     */
    private $order_list_service;

    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\CustomerService
     */
    private $customer_service;

    /**
     * @var \App\Services\Master\DeliveryDestinationService
     */
    private $delivery_destination_service;

    /**
     * @var \App\Services\Master\FactoryProductService
     */
    private $factory_product_service;

    /**
     * @var \App\Services\Order\VVFBackboneImportService
     */
    private $vvf_backbone_import_service;

    /**
     * @param  \App\Services\Order\OrderListService $order_list_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     * @param  \App\Services\Master\FactoryProductService $factory_product_service
     * @param  \App\Services\Order\VVFBackboneImportService $vvf_backbone_import_service
     * @return void
     */
    public function __construct(
        OrderListService $order_list_service,
        FactoryService $factory_service,
        CustomerService $customer_service,
        DeliveryDestinationService $delivery_destination_service,
        FactoryProductService $factory_product_service,
        VVFBackboneImportService $vvf_backbone_import_service
    ) {
        parent::__construct();

        $this->order_list_service = $order_list_service;
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->delivery_destination_service = $delivery_destination_service;
        $this->factory_product_service = $factory_product_service;
        $this->vvf_backbone_import_service = $vvf_backbone_import_service;
    }

    /**
     * 注文一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $factory = $customer = null;
        $orders = [];

        $params = $request->session()->get('order.order_list.search', []);
        if (count($params) !== 0) {
            $factory = $this->factory_service->find($params['factory_code']);
            $customer = $this->customer_service->find($params['customer_code']);

            try {
                $page = $request->page ?: 1;
                $order = $request->only(['sort', 'order']);

                $orders = $this->order_list_service->searchOrders($params, (int)$page, $order);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('order.order_list.index');
            }
        }

        return view('order.order_list.index')->with(compact('params', 'factory', 'customer', 'orders'));
    }

    /**
     * 注文一覧 検索
     *
     * @param  \App\Http\Requests\Order\SearchOrdersRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchOrdersRequest $request): RedirectResponse
    {
        $request->session()->put('order.order_list.search', $request->all());
        return redirect()->route('order.order_list.index');
    }

    /**
     * 注文一覧 Excel出力
     *
     * @param \App\Http\Requests\Order\SearchOrdersRequest $request
     */
    public function export(SearchOrdersRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $customer = $this->customer_service->find($request->customer_code);

        try {
            $this->order_list_service->exportOrders($request->all(), $factory, $customer);
        } catch (LaravelExcelException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['excel_fail']]);
        }
    }

    /**
     * 注文一覧 仮注文マッチング
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function match(Request $request): RedirectResponse
    {
        $matching_message = $this->vvf_backbone_import_service->matching();
        if (isset($matching_message['stop_message'])) {
            return redirect()->back()->with(['alert' => [
                'class' => 'warning',
                'message' => $matching_message['stop_message']
            ]]);
        }

        return redirect()->back()->with(['alert' => [
            'class' => 'info',
            'message' => $matching_message['success_message']
        ]]);
    }

    /**
     * 注文データ変更
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Order $order)
    {
        if ($order->isRelatedTemporaryOrder()) {
            return redirect()->route('order.order_list.index');
        }

        return view('order.order_list.edit')->with(compact('order'));
    }

    /**
     * 注文データ変更 更新
     *
     * @param  \App\Http\Requests\Order\UpdateOrderRequest $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        if ($order->isRelatedTemporaryOrder()) {
            return redirect()->route('order.order_list.index');
        }
        if ($request->updated_at !== $order->updated_at->format('Y-m-d H:i:s')) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        }

        try {
            $factory_product = $this->factory_product_service->findFactoryProduct([
                'factory_code' => $order->factory_code,
                'factory_product_sequence_number' => $request->factory_product_sequence_number
            ]);

            if (! $order->canUpdateFactoryProduct($factory_product)) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['has_allocated']]);
            }

            $this->order_list_service->updateOrder($request->all(), $order, $factory_product);
        } catch (OptimisticLockException $e) {
            report($e);
            return redirect()
                ->back()
                ->withInput()
                ->with(['alert' => $this->operations['exceeding_allocation_quantity']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('order.order_list.edit', $order->order_number)
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 注文データキャンセル
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($request->updated_at !== $order->updated_at->format('Y-m-d H:i:s')) {
            return redirect()->back()->with(['alert' => $this->operations['interuptted']]);
        }
        if ($order->isCanceledOrder() || $order->factory_cancel_flag) {
            return redirect()->back()->with(['alert' => $this->operations['canceled']]);
        }
        if ($order->isAllocated()) {
            return redirect()->back()->with(['alert' => $this->operations['allocated']]);
        }
        if ($order->hadBeenShipped()) {
            return redirect()->back()->with(['alert' => $this->operations['shipped']]);
        }

        try {
            $this->order_list_service->cancelOrder($order);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 赤黒伝票 登録
     *
     * @param  \App\Http\Requests\Order\SaveSlipRequest $request
     * @return void
     */
    public function saveSlip(SaveSlipRequest $request): void
    {
        if ($request->ajax()) {
            $factory = $this->factory_service->find($request->factory_code);
            $delivery_destination = $this->delivery_destination_service->find($request->delivery_destination_code);

            try {
                $this->order_list_service->saveSlip($request->all(), $factory, $delivery_destination);
                return;
            } catch (PDOException $e) {
                report($e);
                abort(500);
            }
        }

        abort(403);
    }

    /**
     * 未紐づけ確定注文 検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function searchFixedOrders(Request $request): OrderCollection
    {
        if ($request->ajax()) {
            return $this->order_list_service->searchLinkableFixedOrders($request->all());
        }

        abort(404);
    }

    /**
     * 紐付設定 登録
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\Response
     */
    public function link(Request $request, Order $order): Response
    {
        if ($request->ajax()) {
            try {
                $this->order_list_service->linkOrders($order, $request->order_number_list ?: []);
                return response('linked successfully.');
            } catch (OptimisticLockException $e) {
                return response('interrupted.', Response::HTTP_LOCKED);
            } catch (DisabledToLinkOrderException $e) {
                return response($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (PDOException $e) {
                report($e);
                abort(500);
            }
        }

        abort(403);
    }

    /**
     * 紐付設定 解除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\Response
     */
    public function cancelLink(Request $request, Order $order): Response
    {
        if ($request->ajax()) {
            try {
                $this->order_list_service->cancelLinkOrders($order);
                return response('cenceled link successfully.');
            } catch (OptimisticLockException $e) {
                return response('interrupted.', Response::HTTP_LOCKED);
            } catch (PDOException $e) {
                report($e);
                abort(500);
            }
        }

        abort(403);
    }
}
