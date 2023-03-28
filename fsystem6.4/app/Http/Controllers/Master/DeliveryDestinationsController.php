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
use App\Http\Requests\Master\CreateDeliveryDestinationRequest;
use App\Http\Requests\Master\SearchDeliveryDestinationsRequest;
use App\Http\Requests\Master\UpdateDeliveryDestinationRequest;
use App\Models\Master\DeliveryDestination;
use App\Services\Master\DeliveryDestinationService;
use App\Services\Master\DeliveryFactoryProductService;
use App\Services\Master\WarehouseService;

class DeliveryDestinationsController extends Controller
{
    /**
     * @var \App\Services\Master\DeliveryDestinationService
     */
    private $delivery_destination_service;

    /**
     * @var \App\Services\Master\WarehouseService
     */
    private $warehouse_service;

    /**
     * @var \App\Services\Master\DeliveryFactoryProductService
     */
    private $delivery_factory_product_service;

    /**
     * @param  \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     * @param  \App\Services\Master\WarehouseService $warehouse_service
     * @param  \App\Services\Master\DeliveryFactoryProductService $delivery_factory_product_service
     * @return void
     */
    public function __construct(
        DeliveryDestinationService $delivery_destination_service,
        WarehouseService $warehouse_service,
        DeliveryFactoryProductService $delivery_factory_product_service
    ) {
        parent::__construct();

        $this->delivery_destination_service = $delivery_destination_service;
        $this->warehouse_service = $warehouse_service;
        $this->delivery_factory_product_service = $delivery_factory_product_service;
    }

    /**
     * 納入先マスタ 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $delivery_destinations = [];

        $params = $request->session()->get('master.delivery_destinations.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $delivery_destinations = $this->delivery_destination_service
                    ->searchDeliveryDestinations($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.delivery_destinations.index');
            }
        }

        return view('master.delivery_destinations.index')->with(compact('delivery_destinations', 'params'));
    }

    /**
     * 納入先マスタ 検索
     *
     * @param  \App\Http\Requests\Master\SearchDeliveryDestinationsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchDeliveryDestinationsRequest $request): RedirectResponse
    {
        $request->session()->put('master.delivery_destinations.search', $request->all());
        return redirect()->route('master.delivery_destinations.index');
    }

    /**
     * 納入先マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.delivery_destinations.add');
    }

    /**
     * 納入先マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateDeliveryDestinationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateDeliveryDestinationRequest $request): RedirectResponse
    {
        try {
            $delivery_destination = $this->delivery_destination_service->createDeliveryDestination($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.delivery_destinations.edit', $delivery_destination->delivery_destination_code)
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入先マスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, DeliveryDestination $delivery_destination): View
    {
        return view('master.delivery_destinations.edit')->with(compact('delivery_destination'));
    }

    /**
     * 納入先マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateDeliveryDestinationsRequest $request
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateDeliveryDestinationRequest $request,
        DeliveryDestination $delivery_destination
    ): RedirectResponse {
        try {
            $this->delivery_destination_service->updateDeliveryDestination($delivery_destination, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.delivery_destinations.edit', $delivery_destination->delivery_destination_code)
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入先マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, DeliveryDestination $delivery_destination): RedirectResponse
    {
        if (! $delivery_destination->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->delivery_destination_service->deleteDeliveryDestination($delivery_destination);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入先マスタ 倉庫一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\View\View
     */
    public function warehouses(Request $request, DeliveryDestination $delivery_destination): View
    {
        $warehouses = $this->warehouse_service->getNotLinkedWarehouses($delivery_destination);
        return view('master.delivery_destinations.warehouses')->with(compact('delivery_destination', 'warehouses'));
    }

    /**
     * 納入先マスタ 商品一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\View\View
     */
    public function factoryProducts(Request $request, DeliveryDestination $delivery_destination): View
    {
        $page = $request->page ?: 1;
        $delivery_factory_products = $this->delivery_factory_product_service->getFactoryProductsByDeliveryDestination(
            $delivery_destination,
            (int)$page
        );

        return view('master.delivery_destinations.factory_products')->with(
            compact('delivery_destination', 'delivery_factory_products')
        );
    }

    /**
     * API用 納入先検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function searchDeliveryDestinations(Request $request): array
    {
        if ($request->ajax()) {
            return $this->delivery_destination_service->searchDeliveryDestinationsForSearchingApi($request->all());
        }

        abort(404);
    }
}
