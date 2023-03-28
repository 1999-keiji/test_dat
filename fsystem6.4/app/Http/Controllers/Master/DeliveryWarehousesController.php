<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Exceptions\OptimisticLockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateDeliveryWarehouseRequest;
use App\Http\Requests\Master\GetShippingDateRequest;
use App\Http\Requests\Master\SearchDeliveryWarehousesRequest;
use App\Http\Requests\Master\UpdateDeliveryWarehouseRequest;
use App\Models\Master\DeliveryWarehouse;
use App\Services\Master\DeliveryDestinationService;
use App\Services\Master\DeliveryWarehouseService;
use App\Services\Master\FactoryService;
use App\ValueObjects\Date\DeliveryDate;

class DeliveryWarehousesController extends Controller
{
    /**
     * @var \App\Services\Master\DeliveryWarehouseService
     */
    private $delivery_warehouse_service;

    /**
     * @var \App\Services\Master\DeliveryDestinationService
     */
    private $delivery_destination_service;

    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param  \App\Services\Master\DeliveryWarehouseService $delivery_warehouse_service
     * @param  \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @return void
     */
    public function __construct(
        DeliveryWarehouseService $delivery_warehouse_service,
        DeliveryDestinationService $delivery_destination_service,
        FactoryService $factory_service
    ) {
        parent::__construct();

        $this->delivery_warehouse_service = $delivery_warehouse_service;
        $this->delivery_destination_service = $delivery_destination_service;
        $this->factory_service = $factory_service;
    }

    /**
     * リードタイム一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $delivery_warehouses = [];

        $params = $request->session()->get('master.delivery_warehouses.search', []);
        if (count($params) !== 0) {
            $order = $request->only(['sort', 'order']);
            $page = $request->page ?: 1;

            try {
                $delivery_warehouses = $this->delivery_warehouse_service->search($params, $order, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.lead_time.index');
            }
        }

        return view('master.delivery_warehouses.index')->with(compact('delivery_warehouses', 'params'));
    }

    /**
     * リードタイム検索
     *
     * @param  \App\Http\Requests\Master\SearchDeliveryWarehousesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchDeliveryWarehousesRequest $request): RedirectResponse
    {
        $request->session()->put('master.delivery_warehouses.search', $request->all());
        return redirect()->route('master.lead_time.index');
    }

    /**
     * 納入倉庫マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateDeliveryWarehouseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateDeliveryWarehouseRequest $request): RedirectResponse
    {
        try {
            $delivery_warehouse = $this->delivery_warehouse_service->createDeliveryWarehouse($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入倉庫マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateDeliveryDestinationsRequest $request
     * @param  \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateDeliveryWarehouseRequest $request,
        DeliveryWarehouse $delivery_warehouse
    ): RedirectResponse {
        try {
            $this->delivery_warehouse_service->updateDeliveryWarehouse($delivery_warehouse, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入倉庫マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\DeliveryWarehouse $delivery_warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, DeliveryWarehouse $delivery_warehouse): RedirectResponse
    {
        try {
            $this->delivery_warehouse_service->deleteDeliveryWarehouse($delivery_warehouse);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 出荷日の計算
     *
     * @param  \App\Http\Requests\GetShippingDateRequest $request
     * @return string
     */
    public function getShippingDateByDeliveryDestinationAndFactory(GetShippingDateRequest $request): string
    {
        if ($request->ajax()) {
            $delivery_destination = $this->delivery_destination_service->find($request->delivery_destination_code);
            $factroy = $this->factory_service->find($request->factory_code);

            return DeliveryDate::parse($request->delivery_date)
                ->getShippingDate($delivery_destination, $factroy)
                ->value();
        }

        abort(404);
    }
}
