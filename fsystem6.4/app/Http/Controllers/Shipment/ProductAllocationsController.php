<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OverAllocationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\SaveProductAllocationsRequest;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Services\Order\OrderService;
use App\Services\Shipment\ProductAllocationService;
use App\Services\Stock\StockService;
use App\ValueObjects\Date\HarvestingDate;

class ProductAllocationsController extends Controller
{
    /**
     * @var \App\Services\Shipment\ProductAllocationService
     */
    private $product_allocation_service;

    /**
     * @var \App\Services\Stock\StockService
     */
    private $stock_service;

    /**
     * @var \App\Services\Order\OrderService
     */
    private $order_service;

    /**
     * @param  \App\Services\Shipment\ProductAllocationService $product_allocation_service
     * @param  \App\Services\Shipment\StockService $stock_service
     * @param  \App\Services\Order\OrderService $order_service
     * @return void
     */
    public function __construct(
        ProductAllocationService $product_allocation_service,
        StockService $stock_service,
        OrderService $order_service
    ) {
        parent::__construct();

        $this->product_allocation_service = $product_allocation_service;
        $this->stock_service = $stock_service;
        $this->order_service = $order_service;
    }

    /**
     * 在庫引当 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory, Species $species, HarvestingDate $harvesting_date): View
    {
        $packaging_styles = $factory->factory_products->getPackagingStylesBySpecies($species);
        $shipping_dates = $harvesting_date->toListOfShippingDatesToAllocateProducts();

        $wareshouses = $factory->getAllocatableWarehouses();
        $factory_products = [];

        if ($packaging_style = $request->only(['number_of_heads', 'weight_per_number_of_heads', 'input_group'])) {
            $warehouse_code = $request->get('warehouse_code', $factory->get);

            $wareshouses = $wareshouses
                ->map(function ($w) use ($factory, $species, $harvesting_date, $packaging_style) {
                    $w->stock = $this->stock_service
                        ->getStocksPerHarvestingDate($factory, $species, $harvesting_date, $packaging_style, $w);

                    return $w;
                });

            $factory_products = $this->order_service->getAllocatedFactoryProductsWithOrders(
                $factory,
                $species,
                $shipping_dates,
                $packaging_style,
                $wareshouses->findByWarehouseCode($request->warehouse_code ?: '') ?: $factory->getDefaultWarehouse()
            );
        }

        return view('shipment.product_allocations.index')->with(compact(
            'factory',
            'species',
            'packaging_styles',
            'shipping_dates',
            'packaging_style',
            'wareshouses',
            'factory_products'
        ));
    }

    /**
     * 在庫引当 保存
     *
     * @param  \App\Http\Requests\Shipment\SaveProductAllocationsRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(
        SaveProductAllocationsRequest $request,
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ) : RedirectResponse {
        $warehouse = $factory->getAllocatableWarehouses()->findByWarehouseCode($request->warehouse_code);
        $orders = $this->order_service->getNotDeliveredOrdersByShippingDate(
            $factory,
            $species,
            $harvesting_date->toListOfShippingDatesToAllocateProducts(),
            $request->factory_product_sequence_numbers,
            $warehouse
        );

        try {
            $this->product_allocation_service
                ->saveProductAllocations($request->factory_products ?: [], $warehouse, $orders);
        } catch (OverAllocationException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['over_allocated']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
