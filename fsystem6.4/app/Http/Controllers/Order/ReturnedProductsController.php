<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Exceptions\PageOverException;
use App\Exceptions\OptimisticLockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateReturnedProductRequest;
use App\Http\Requests\Order\SearchReturnedProductsRequest;
use App\Http\Requests\Order\UpdateReturnedProductRequest;
use App\Models\Order\Order;
use App\Services\Master\FactoryProductService;
use App\Services\Order\OrderService;
use App\Services\Order\ReturnedProductService;

class ReturnedProductsController extends Controller
{
    /**
     * @var \App\Services\Order\OrderService
     */
    private $order_service;

    /**
     * @var \App\Services\Plan\ReturnedProductService
     */
    private $returned_product_service;

    /**
     * @var \App\Services\Master\FactoryProductService
     */
    private $factory_product_service;

    /**
     * @param  \App\Services\Order\OrderService $order_service
     * @param  \App\Services\Order\ReturnedProductService $returned_product_service
     * @param  \App\Services\Master\FactoryProductService $factory_product_service
     * @return void
     */
    public function __construct(
        OrderService $order_service,
        ReturnedProductService $returned_product_service,
        FactoryProductService $factory_product_service
    ) {
        parent::__construct();

        $this->order_service = $order_service;
        $this->returned_product_service = $returned_product_service;
        $this->factory_product_service = $factory_product_service;
    }

    /**
     * 返品入力 画面
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $orders = [];

        $params = $request->session()->get('order.returned_products.search', []);
        if (count($params) !== 0) {
            try {
                $page = $request->page ?: 1;
                $order = $request->only(['sort', 'order']);

                $orders = $this->order_service->getOrdersWithReturnedProduct($params, (int)$page, $order);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('order.returned_products.index');
            }
        }

        return view('order.returned_products.index')->with(compact('orders', 'params'));
    }

    /**
     * 返品入力 検索
     *
     * @param  \App\Http\Requests\Order\SearchReturnedProductsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchReturnedProductsRequest $request): RedirectResponse
    {
        $request->session()->put('order.returned_products.search', $request->all());
        return redirect()->route('order.returned_products.index');
    }

    /**
     * 返品入力 登録
     *
     * @param  \App\Http\Requests\Order\CreateReturnedProductRequest $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateReturnedProductRequest $request, Order $order): RedirectResponse
    {
        $factory_product = $this->factory_product_service->findFactoryProduct([
            'factory_code' => $order->factory_code,
            'factory_product_sequence_number' => $request->factory_product_sequence_number
        ]);

        try {
            $this->returned_product_service->createReturnedProduct($order, $factory_product, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 返品入力 更新
     *
     * @param  \App\Http\Requests\Master\UpdateReturnedProductRequest $request
     * @param  \App\Models\Order\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateReturnedProductRequest $request, Order $order): RedirectResponse
    {
        $factory_product = $this->factory_product_service->findFactoryProduct([
            'factory_code' => $order->factory_code,
            'factory_product_sequence_number' => $request->factory_product_sequence_number
        ]);

        try {
            $this->returned_product_service
                ->updateReturnedProduct($order->returned_product, $factory_product, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
