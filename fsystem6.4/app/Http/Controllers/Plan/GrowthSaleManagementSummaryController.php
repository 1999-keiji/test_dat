<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plan;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\SearchGrowthSaleManagementSummaryRequest;
use App\Services\Master\DeliveryDestinationService;
use App\Services\Master\FactoryService;
use App\Services\Master\SpeciesService;
use App\Services\Order\OrderForecastService;
use App\Services\Order\OrderService;
use App\Services\Plan\CropService;
use App\Services\Plan\PanelStateService;
use App\ValueObjects\Date\HarvestingDate;

class GrowthSaleManagementSummaryController extends Controller
{
    /**
     * @var \App\Services\Master\SpeciesService $species_service
     */
    private $species_service;

    /**
     * @var \App\Services\Master\FactoryService $factory_service
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     */
    private $delivery_destination_service;

    /**
     * @var \App\Services\Plan\PanelStateService $panel_state_service
     */
    private $panel_state_service;

    /**
     * @var \App\Services\Plan\CropService $crop_service
     */
    private $crop_service;

    /**
     * @var \App\Services\Order\OrderForecastService $order_forecast_service
     */
    private $order_forecast_service;

    /**
     * @var \App\Services\Order\OrderService $order_service
     */
    private $order_service;

    /**
     * @param  \App\Services\Master\SpeciesService $species_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     * @param  \App\Services\Plan\PanelStateService $panel_state_service
     * @param  \App\Services\Plan\CropService $crop_service
     * @param  \App\Services\Order\OrderForecastService $order_forecast_service
     * @param  \App\Services\Order\OrderService $order_service
     * @param  \App\Services\Stock\CarryOverStockService $carry_over_stock_service
     * @return void
     */
    public function __construct(
        SpeciesService $species_service,
        FactoryService $factory_service,
        DeliveryDestinationService $delivery_destination_service,
        PanelStateService $panel_state_service,
        CropService $crop_service,
        OrderForecastService $order_forecast_service,
        OrderService $order_service
    ) {
        parent::__construct();

        $this->species_service = $species_service;
        $this->factory_service = $factory_service;
        $this->delivery_destination_service = $delivery_destination_service;
        $this->panel_state_service = $panel_state_service;
        $this->crop_service = $crop_service;
        $this->order_forecast_service = $order_forecast_service;
        $this->order_service = $order_service;
    }

    /**
     * 生産・販売管理表サマリー
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $params = $request->session()->get('plan.growth_sale_management_summary.search', $request->old());
        return view('plan.growth_sale_management_summary.index')->with(compact('params'));
    }

    /**
     * 生産・販売管理表サマリー 検索
     *
     * @param  \App\Http\Requests\Plan\SearchGrowthSaleManagementSummaryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchGrowthSaleManagementSummaryRequest $request): RedirectResponse
    {
        $request->session()->put('plan.growth_sale_management_summary.search', $request->all());
        if ($request->display_type === 'factories') {
            return redirect()->route('plan.growth_sale_management_summary.factories');
        }
        if ($request->display_type === 'factory_species') {
            return redirect()->route('plan.growth_sale_management_summary.factory_species');
        }
        if ($request->display_type === 'delivery_destination') {
            return redirect()->route('plan.growth_sale_management_summary.delivery_destination');
        }

        return redirect()->route('plan.growth_sale_management_summary.index');
    }

    /**
     * 生産・販売管理表サマリー 全工場
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function factories(Request $request)
    {
        $params = $request->session()->get('plan.growth_sale_management_summary.search', []);
        if (count($params) === 0) {
            return redirect()->route('plan.growth_sale_management_summary.index');
        }
        if ($params['display_type'] !== 'factories') {
            return redirect()->route('plan.growth_sale_management_summary.index');
        }

        $species = $this->species_service->find($params['species_code']);

        $harvesting_date = $params['display_term'] === 'date' ?
            HarvestingDate::parse($params['display_from_date']) :
            HarvestingDate::createFromYearMonth($params['display_from_month']);

        $panel_states = $this->panel_state_service
            ->getHarvestingStockQuantitiesBySpeciesAndHarvestingDate($params, $harvesting_date);

        $forecasted_product_rates = $this->crop_service
            ->getForecastedProductRatesBySpeciesAndHarvestingDate($params, $harvesting_date);

        $crops = $this->crop_service->getCropsBySpeciesAndHarvestingDate($params, $harvesting_date);

        $order_forecasts = $this->order_forecast_service
            ->getOrderForecastsBySpeciesAndHarvestingDate($params, $harvesting_date);

        $factories_with_order = $this->order_service
            ->summarizeOrdersPerFactory($params, $species, $harvesting_date, $order_forecasts);

        $factories_with_summary = $this->panel_state_service->summarizePerFactory(
            $params,
            $harvesting_date,
            $panel_states,
            $forecasted_product_rates,
            $crops,
            $factories_with_order
        );

        return view('plan.growth_sale_management_summary.factories')->with(
            compact('params', 'species', 'harvesting_date', 'factories_with_summary')
        );
    }

    /**
     * 生産・販売管理表サマリー 工場-品種単位
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function factorySpecies(Request $request)
    {
        $params = $request->session()->get('plan.growth_sale_management_summary.search', []);
        if (count($params) === 0) {
            return redirect()->route('plan.growth_sale_management_summary.index');
        }
        if ($params['display_type'] !== 'factory_species') {
            return redirect()->route('plan.growth_sale_management_summary.index');
        }

        $factory = $this->factory_service->find($params['factory_code']);
        $species = $this->species_service->find($params['species_code']);

        $harvesting_date = $params['display_term'] === 'date' ?
            HarvestingDate::parse($params['display_from_date']) :
            HarvestingDate::createFromYearMonth($params['display_from_month']);

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

        return view('plan.growth_sale_management_summary.factory_species')->with(
            compact('params', 'factory', 'species', 'harvesting_date', 'factory_products_with_order', 'summary')
        );
    }

    /**
     * 生産・販売管理表サマリー 納入先単位
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function deliveryDestination(Request $request)
    {
        $params = $request->session()->get('plan.growth_sale_management_summary.search', []);
        if (count($params) === 0) {
            return redirect()->route('plan.growth_sale_management_summary.index');
        }
        if ($params['display_type'] !== 'delivery_destination') {
            return redirect()->route('plan.growth_sale_management_summary.index');
        }

        $species = $this->species_service->find($params['species_code']);
        $delivery_destination = $this->delivery_destination_service->find($params['delivery_destination_code']);

        $harvesting_date = $params['display_term'] === 'date' ?
            HarvestingDate::parse($params['display_from_date']) :
            HarvestingDate::createFromYearMonth($params['display_from_month']);

        $order_forecasts = $this->order_forecast_service
            ->getOrderForecastsBySpeciesAndHarvestingDate($params, $harvesting_date);

        $factories_with_order = $this->order_service
            ->summarizeOrdersPerFactoryProduct($params, $harvesting_date, $order_forecasts);

        return view('plan.growth_sale_management_summary.delivery_destination')->with(
            compact('params', 'species', 'delivery_destination', 'harvesting_date', 'factories_with_order')
        );
    }
}
