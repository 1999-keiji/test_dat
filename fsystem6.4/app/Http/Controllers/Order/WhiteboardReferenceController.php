<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\SearchWhiteboardReferenceRequest;
use App\Services\Master\FactoryService;
use App\Services\Master\DeliveryFactoryProductService;
use App\Services\Master\SpeciesService;
use App\Services\Order\OrderService;
use App\Services\Order\OrderForecastService;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;

class WhiteboardReferenceController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService $factory_service
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\SpeciesService $species_service
     */
    private $species_service;

    /**
     * @var \App\Services\Master\DeliveryFactoryProductService
     */
    private $delivery_factory_product_service;

    /**
     * @var \App\Services\Plan\OrderService
     */
    private $order_service;

    /**
     * @var \App\Services\Plan\OrderForecastService
     */
    private $order_forecast_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\SpeciesService $species_service
     * @param  \App\Services\Master\DeliveryFactoryProductService $delivery_factory_product_service
     * @param  \App\Services\Order\OrderService $order_service
     * @param  \App\Services\Order\OrderForecastService $order_forecast_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        SpeciesService $species_service,
        DeliveryFactoryProductService $delivery_factory_product_service,
        OrderService $order_service,
        OrderForecastService $order_forecast_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->species_service = $species_service;
        $this->delivery_factory_product_service = $delivery_factory_product_service;
        $this->order_service = $order_service;
        $this->order_forecast_service = $order_forecast_service;
    }

    /**
     * ホワイトボード情報参照 画面表示
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $packaging_styles = $delivery_dates = [];

        $params = $request->session()->get('order.whiteboard_reference.search', []);
        if (count($params) !== 0) {
            $factory = $this->factory_service->find($params['factory_code']);
            $species = $this->species_service->find($params['species_code']);
            $year_month = $params['output_date'] === 'shipping_date' ?
                ShippingDate::createFromYearMonth($params['year_month']) :
                DeliveryDate::createFromYearMonth($params['year_month']);

            $orders = $this->order_service->getOrdersWithPreviousOrderQuantity($factory, $species, $year_month, true);
            $order_forecasts = $this->order_forecast_service
                ->getOrderForecastsByFactoryAndSpecies($factory, $species, $year_month, true);

            $packaging_styles = $this->delivery_factory_product_service
                ->getGroupedDeliveryFactoryProductsPerPackagingStyle($params, $orders);

            $dates = $this->order_service
                ->getOrderQuantitiesPerDates($year_month, $packaging_styles, $orders, $order_forecasts);
        }

        return view('order.whiteboard_reference.index')->with(compact('params', 'packaging_styles', 'dates'));
    }

    /**
     * ホワイトボード情報参照 検索
     *
     * @param  \App\Http\Requests\Order\SearchWhiteboardReferenceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchWhiteboardReferenceRequest $request): RedirectResponse
    {
        $request->session()->put('order.whiteboard_reference.search', $request->all());
        return redirect()->route('order.whiteboard_reference.index');
    }

    /**
     * ホワイトボード情報参照 出力
     *
     * @param \App\Http\Requests\Order\SearchWhiteboardReferenceRequest $request
     */
    public function export(SearchWhiteboardReferenceRequest $request)
    {
        $params = $request->all();

        $factory = $this->factory_service->find($params['factory_code']);
        $species = $this->species_service->find($params['species_code']);
        $year_month = $params['output_date'] === 'shipping_date' ?
            ShippingDate::createFromYearMonth($params['year_month']) :
            DeliveryDate::createFromYearMonth($params['year_month']);

        $orders = $this->order_service->getOrdersWithPreviousOrderQuantity($factory, $species, $year_month, true);
        $order_forecasts = $this->order_forecast_service
                ->getOrderForecastsByFactoryAndSpecies($factory, $species, $year_month, true);

        $packaging_styles = $this->delivery_factory_product_service
                ->getGroupedDeliveryFactoryProductsPerPackagingStyle($params, $orders);

        $this->order_service
            ->exportOrderQuantitiesPerDeliveryDate(
                $year_month,
                $factory,
                $species,
                $packaging_styles,
                $orders,
                $order_forecasts
            );
    }
}
