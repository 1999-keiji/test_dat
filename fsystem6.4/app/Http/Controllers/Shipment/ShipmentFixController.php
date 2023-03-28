<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\FixShippingOrderedProductsRequest;
use App\Http\Requests\Shipment\SearchShipmentFixRequest;
use App\Services\Order\OrderService;

class ShipmentFixController extends Controller
{
    /**
     * @var \App\Services\Shipment\OrderService
     */
    private $order_service;

    /**
     * @param  \App\Services\Shipment\OrderService $order_service
     * @return void
     */
    public function __construct(OrderService $order_service)
    {
        parent::__construct();

        $this->order_service = $order_service;
    }

    /**
     * 出荷確定 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $orders = [];

        $params = $request->session()->get('shipment.shipment_fix.search', []);
        if (count($params) !== 0) {
            $orders = $this->order_service->getShippableOrders($params);
        }

        return view('shipment.shipment_fix.index')->with(compact('orders', 'params'));
    }

    /**
     * 出荷確定 検索
     *
     * @param  \App\Http\Requests\Shipment\SearchShipmentFixRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchShipmentFixRequest $request): RedirectResponse
    {
        $request->session()->put('shipment.shipment_fix.search', $request->all());
        return redirect()->route('shipment.shipment_fix.index');
    }

    /**
     * 出荷確定 確定
     *
     * @param  \App\Http\Requests\Shipment\FixShippingOrderedProductsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fix(FixShippingOrderedProductsRequest $request): RedirectResponse
    {
        try {
            $this->order_service->fixShippingOrderedProducts($request->order_numbers);
        } catch (OptimisticLockException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('shipment.shipment_fix.index')->with(['alert' => $this->operations['success']]);
    }
}
