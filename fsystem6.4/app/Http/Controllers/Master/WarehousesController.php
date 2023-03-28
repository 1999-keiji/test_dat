<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateWarehouseRequest;
use App\Http\Requests\Master\SearchWarehouseRequest;
use App\Http\Requests\Master\UpdateWarehouseRequest;
use App\Models\Master\Warehouse;
use App\Services\Master\DeliveryWarehouseService;
use App\Services\Master\WarehouseService;

class WarehousesController extends Controller
{
    /**
     * @var \App\Services\Master\WarehouseService
     */
    private $warehouse_service;

    /**
     * @var \App\Services\Master\DeliveryWarehouseService
     */
    private $delivery_warehouse_service;

    /**
     * @param  \App\Services\Master\WarehouseService $warehouse_service
     * @param  \App\Services\Master\DeliveryWarehouseService $delivery_warehouse_service
     * @return void
     */
    public function __construct(
        WarehouseService $warehouse_service,
        DeliveryWarehouseService $delivery_warehouse_service
    ) {
        parent::__construct();

        $this->warehouse_service = $warehouse_service;
        $this->delivery_warehouse_service = $delivery_warehouse_service;
    }

    /**
     * 倉庫マスタ一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $warehouses = [];

        $params = $request->session()->get('master.warehouses.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $warehouses = $this->warehouse_service->searchWarehouses($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.warehouses.index');
            }
        }

        return view('master.warehouses.index')->with(compact('warehouses', 'params'));
    }

    /**
     * 倉庫マスタ検索
     *
     * @param \App\Http\Requests\Master\SearchWarehouseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchWarehouseRequest $request): RedirectResponse
    {
        $request->session()->put('master.warehouses.search', $request->all());
        return redirect()->route('master.warehouses.index');
    }

    /**
     * 倉庫マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.warehouses.add');
    }

    /**
     * 倉庫マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateWarehouseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateWarehouseRequest $request): RedirectResponse
    {
        try {
            $warehouse = $this->warehouse_service->createWarehouse($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.warehouses.edit', $warehouse->warehouse_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 倉庫マスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Warehouse $warehouse): View
    {
        return view('master.warehouses.edit')->with(compact('warehouse'));
    }

    /**
     * 倉庫マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateWarehouseRequest $request
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        try {
            $this->warehouse_service->updateWarehouse($warehouse, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.warehouses.edit', $warehouse->warehouse_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 倉庫マスタ削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Warehouse $warehouse): RedirectResponse
    {
        if (! $warehouse->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->warehouse_service->deleteWarehouse($warehouse);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 倉庫マスタ 工場倉庫一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \Illuminate\View\View
     */
    public function factoryWarehouses(Request $request, Warehouse $warehouse): View
    {
        return view('master.warehouses.factory_warehouses')->with(compact('warehouse'));
    }

    /**
     * 倉庫マスタ 納入倉庫一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \Illuminate\View\View
     */
    public function deliveryWarehouses(Request $request, Warehouse $warehouse): View
    {
        $order = $request->only(['sort', 'order']);
        $page = $request->page ?: 1;
        $delivery_warehouses = $this->delivery_warehouse_service
            ->getDeliveryWarehousesByWarehouse($warehouse, $order, (int)$page);

        return view('master.warehouses.delivery_warehouses')->with(compact('warehouse', 'delivery_warehouses'));
    }
}
