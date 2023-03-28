<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateFactoryWarehousRequest;
use App\Http\Requests\Master\UpdateFactoryWarehousRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactoryWarehouse;
use App\Services\Master\FactoryWarehouseService;
use App\Services\Master\WarehouseService;

class FactoryWarehousesController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryWarehouseService
     */
    private $factory_warehouse_service;

    /**
     * @var \App\Services\Master\WarehouseService
     */
    private $warehouse_service;

    /**
     * @param  \App\Services\Master\FactoryWarehouseService $factory_warehouse_service
     * @param  \App\Services\Master\WarehouseService $warehouse_service
     * @return void
     */
    public function __construct(
        FactoryWarehouseService $factory_warehouse_service,
        WarehouseService $warehouse_service
    ) {
        parent::__construct();

        $this->factory_warehouse_service = $factory_warehouse_service;
        $this->warehouse_service = $warehouse_service;
    }

    /**
     * 工場マスタ 倉庫タブ 表示
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory): View
    {
        $factory_warehouses = $factory->factory_warehouses->sortByPriority();
        $not_linked_warehouses = $this->warehouse_service->getNotLinkedWarehousesByFactory($factory);

        return view('master.factories.warehouses')->with(compact(
            'factory',
            'factory_warehouses',
            'not_linked_warehouses'
        ));
    }

    /**
     * 工場倉庫マスタ 倉庫追加
     *
     * @param  \App\Http\Requests\Master\CreateFactoryWarehousRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateFactoryWarehousRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_warehouse_service->linkWarehouse($factory, $request->warehouse_code);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->withInput()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場倉庫マスタ 優先度保存
     *
     * @param  \App\Http\Requests\Master\UpdateFactoryWarehousRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateFactoryWarehousRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_warehouse_service->updateFactoryWarehouse($factory, $request->priorities);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場倉庫マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryWarehouse $factory_warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Factory $factory, FactoryWarehouse $factory_warehouse): RedirectResponse
    {
        if (! $factory_warehouse->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->factory_warehouse_service->deleteFactoryWarehouse($factory, $factory_warehouse);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 工場コードによる倉庫検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getWarehousesWithFactoryCode(Request $request): array
    {
        if ($request->ajax()) {
            return $this->factory_warehouse_service->getWarehousesWithFactoryCodeForSearchingApi($request->all());
        }

        abort(404);
    }
}
