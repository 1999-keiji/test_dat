<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ExportOrderForecastsRequest;
use App\Services\Master\DeliveryFactoryProductService;
use App\Services\Master\FactoryService;
use App\Services\Master\SpeciesService;
use App\Services\Order\OrderForecastService;
use App\Services\Order\OrderService;
use App\Services\Plan\CropService;
use App\Services\Plan\PanelStateService;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\HarvestingDate;

class OrderForecastsController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @var \App\Services\Master\DeliveryFactoryProductService
     */
    private $delivery_factory_product_service;

    /**
     * @var \App\Services\Order\OrderForecastService
     */
    private $order_forecast_service;

    /**
     * @var \App\Services\Order\OrderService
     */
    private $order_service;

    /**
     * @var \App\Services\Plan\PanelStateService $panel_state_service
     */
    private $panel_state_service;

    /**
     * @var \App\Services\Plan\CropService $crop_service
     */
    private $crop_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\SpeciesService $species_service
     * @param  \App\Services\Master\DeliveryFactoryProductService $delivery_factory_product_service
     * @param  \App\Services\Order\OrderForecastService $order_forecast_service
     * @param  \App\Services\Order\OrderService $order_service
     * @param  \App\Services\Plan\PanelStateService $panel_state_service
     * @param  \App\Services\Plan\CropService $crop_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        SpeciesService $species_service,
        DeliveryFactoryProductService $delivery_factory_product_service,
        OrderForecastService $order_forecast_service,
        OrderService $order_service,
        PanelStateService $panel_state_service,
        CropService $crop_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->species_service = $species_service;
        $this->delivery_factory_product_service = $delivery_factory_product_service;
        $this->order_forecast_service = $order_forecast_service;
        $this->order_service = $order_service;
        $this->panel_state_service = $panel_state_service;
        $this->crop_service = $crop_service;
    }

    /**
     * フォーキャストExcel取込 画面
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('order.order_forecasts.index');
    }

    /**
     * フォーキャスト Excel出力
     *
     * @param \App\Http\Requests\Order\ExportOrderForecastsRequest $request
     */
    public function export(ExportOrderForecastsRequest $request)
    {
        $params = $request->all();

        $factory = $this->factory_service->find($params['factory_code']);
        $species = $this->species_service->find($params['species_code']);

        $delivery_date = new DeliveryDate($params['delivery_date']);
        $harvesting_date = (new HarvestingDate($params['harvesting_date']))->toggleFlagOfSlidingToStartOfWeek();

        $factory_products = $this->delivery_factory_product_service
            ->getGroupedDeliveryFactoryProductsPerProduct($params);

        if (count($factory_products) === 0) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['not_found']]);
        }

        $panel_states = $this->panel_state_service
            ->getHarvestingStockQuantitiesBySpeciesAndHarvestingDate($params, $harvesting_date);

        $forecasted_product_rates = $this->crop_service
            ->getForecastedProductRatesBySpeciesAndHarvestingDate($params, $harvesting_date);

        $crops = $this->crop_service->getCropsBySpeciesAndHarvestingDate($params, $harvesting_date);

        $order_forecasts = $this->order_forecast_service
            ->getOrderForecastsBySpeciesAndHarvestingDate($params, $harvesting_date);

        $factory_products_with_order = $this->order_service
            ->summarizeOrdersPerFactoryProductAndDeliveryDestination(
                $params,
                $factory,
                $species,
                $harvesting_date,
                $order_forecasts
            );

        $summary = $this->panel_state_service->summarizeWithFactoryAndSpecies(
            $params,
            $harvesting_date,
            $factory,
            $panel_states,
            $forecasted_product_rates,
            $crops,
            $factory_products_with_order
        );

        $this->order_forecast_service->exportOrderForecasts(
            $factory,
            $species,
            $harvesting_date,
            $summary,
            $delivery_date,
            $factory_products,
            $this->order_service->getOrdersWithPreviousOrderQuantity($factory, $species, $delivery_date)
        );
    }

    /**
     * フォーキャスト Excel取込
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request): RedirectResponse
    {
        $params = $request->all();
        if (! $this->order_forecast_service->checkUploadedFile($params)) {
            return redirect()->back()->with(['alert' => $this->operations['not_matched_file']]);
        }

        try {
            $order_forecasts = $this->order_forecast_service->parseUploadedFile($params);
            if (count($order_forecasts) === 0) {
                return redirect()->back()->with(['alert' => $this->operations['import_data_not_exsit']]);
            }

            $messages = $this->order_forecast_service->importUploadedData($order_forecasts);
            if (count($messages) !== 0) {
                return redirect()->route('order.order_forecasts.index')->with([
                    'alert' => $this->operations['success'],
                    'messages' => $messages
                ]);
            }
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }
    }
}
