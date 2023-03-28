<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderManuallyRequest;
use App\Http\Requests\Order\SearchManualCreatedOrdersRequest;
use App\Http\Requests\Order\UpdateManualCreatedOrderRequest;
use App\Models\Order\Order;
use App\Services\Master\CustomerService;
use App\Services\Master\DeliveryDestinationService;
use App\Services\Master\FactoryService;
use App\Services\Master\FactoryProductService;
use App\Services\Order\OrderInputService;

class OrderInputController extends Controller
{
    /**
     * @var \App\Services\Order\OrderInputService
     */
    private $order_input_service;

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
     * @param  \App\Services\Order\OrderInputService $order_input_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     * @param  \App\Services\Master\FactoryProductService $factory_product_service
     * @return void
     */
    public function __construct(
        OrderInputService $order_input_service,
        FactoryService $factory_service,
        CustomerService $customer_service,
        DeliveryDestinationService $delivery_destination_service,
        FactoryProductService $factory_product_service
    ) {
        parent::__construct();

        $this->order_input_service = $order_input_service;
        $this->factory_service = $factory_service;
        $this->customer_service = $customer_service;
        $this->delivery_destination_service = $delivery_destination_service;
        $this->factory_product_service = $factory_product_service;
    }

    /**
     * 注文入力 画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $orders = [];

        $params = $request->session()->get('order.order_input.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $orders = $this->order_input_service->searchManualCreatedOrders($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('order.order_input.index');
            }
        }

        return view('order.order_input.index')->with(compact('orders', 'params'));
    }

    /**
     * 注文入力 検索
     *
     * @param  \App\Http\Requests\Order\SearchManualCreatedOrdersRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchManualCreatedOrdersRequest $request): RedirectResponse
    {
        $request->session()->put('order.order_input.search', $request->all());
        return redirect()->route('order.order_input.index');
    }

    /**
     * 注文入力 登録
     *
     * @param  \App\Http\Requests\Order\CreateOrderManuallyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateOrderManuallyRequest $request): RedirectResponse
    {
        $factory = $this->factory_service->find($request->factory_code);
        $customer = $this->customer_service->find($request->customer_code);
        $delivery_destination = $this->delivery_destination_service->find($request->delivery_destination_code);
        $factory_product = $this->factory_product_service->findFactoryProduct($request->all());

        try {
            $order = $this->order_input_service->createOrder(
                $request->all(),
                $factory,
                $customer,
                $delivery_destination,
                $factory_product
            );
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('order.order_input.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 注文入力 修正
     *
     * @param  \App\Http\Requests\Order\UpdateManualCreatedOrderRequest $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateManualCreatedOrderRequest $request, Order $order): RedirectResponse
    {
        if (! $order->isUpdatable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }
        if ($order->isAllocated()) {
            return redirect()->back()->with(['alert' => $this->operations['allocated']]);
        }
        if ($order->hadBeenShipped()) {
            return redirect()->back()->with(['alert' => $this->operations['shipped']]);
        }

        $delivery_destination = $this->delivery_destination_service->find($order->delivery_destination_code);
        $factory_product = $this->factory_product_service->findFactoryProduct($request->all());

        try {
            $this->order_input_service->updateOrder($request->all(), $order, $delivery_destination, $factory_product);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('order.order_input.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 注文入力 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Order $order): RedirectResponse
    {
        if ($order->updated_at->format('Y-m-d H:i:s') !== $request->updated_at) {
            return redirect()->back()->with(['alert' => $this->operations['interuptted']]);
        }
        if (! $order->isUpdatable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }
        if ($order->isAllocated()) {
            return redirect()->back()->with(['alert' => $this->operations['allocated']]);
        }
        if ($order->hadBeenShipped()) {
            return redirect()->back()->with(['alert' => $this->operations['shipped']]);
        }

        try {
            $this->order_input_service->deleteOrder($order);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
