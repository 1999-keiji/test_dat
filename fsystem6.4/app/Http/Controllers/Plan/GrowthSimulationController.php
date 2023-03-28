<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plan;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\AddSearchGrowthSimulationRequest;
use App\Http\Requests\Plan\EditSearchGrowthSimulationRequest;
use App\Http\Requests\Plan\SearchFixedGrowthSimulationRequest;
use App\Http\Requests\Plan\SearchGrowthSimulationsRequest;
use App\Models\Plan\GrowthSimulation;
use App\Services\Master\FactoryService;
use App\Services\Order\OrderForecastService;
use App\Services\Order\OrderService;
use App\Services\Plan\CropService;
use App\Services\Plan\GrowthSimulationService;
use App\Services\Plan\PanelStateService;
use App\ValueObjects\Date\HarvestingDate;

class GrowthSimulationController extends Controller
{
    /**
     * @var \App\Services\Plan\GrowthSimulationService
     */
    private $growth_simulation_service;

    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

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
     * @param  \App\Services\Plan\GrowthSimulationService $growth_simulation_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Plan\PanelStateService $panel_state_service
     * @param  \App\Services\Plan\CropService $crop_service
     * @param  \App\Services\Order\OrderForecastService $order_forecast_service
     * @param  \App\Services\Order\OrderService $order_service
     * @param  \App\Services\Stock\CarryOverStockService $carry_over_stock_service
     * @return void
     */
    public function __construct(
        GrowthSimulationService $growth_simulation_service,
        FactoryService $factory_service,
        PanelStateService $panel_state_service,
        CropService $crop_service,
        OrderForecastService $order_forecast_service,
        OrderService $order_service
    ) {
        parent::__construct();

        $this->growth_simulation_service = $growth_simulation_service;
        $this->factory_service = $factory_service;
        $this->panel_state_service = $panel_state_service;
        $this->crop_service = $crop_service;
        $this->order_forecast_service = $order_forecast_service;
        $this->order_service = $order_service;
    }

    /**
     * 生産シミュレーション 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $request->session()->forget('plan.growth_simulation.add');
        $request->session()->forget('plan.growth_simulation.edit');

        $growth_simulations = [];
        $params = $request->session()->get('plan.growth_simulation.search', []);

        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            $order = $request->only(['sort', 'order']);

            try {
                $growth_simulations = $this->growth_simulation_service
                    ->searchGrowthSimulations($params, (int)$page, $order);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('plan.growth_simulation.index');
            }
        }

        return view('plan.growth_simulation.index')->with(compact('growth_simulations', 'params'));
    }

    /**
     * 生産シミュレーション 検索
     *
     * @param  \App\Http\Requests\Plan\SearchGrowthSimulationsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchGrowthSimulationsRequest $request): RedirectResponse
    {
        $request->session()->put('plan.growth_simulation.search', $request->all());
        return redirect()->route('plan.growth_simulation.index');
    }

    /**
     * 生産シミュレーション 入力
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $factory = $factory_species = $harvesting_date = $summary = null;

        $params = $request->session()->get('plan.growth_simulation.add', []);
        if (count($params) !== 0) {
            $factory = $this->factory_service->find($params['factory_code']);
            $factory_species = $factory->factory_species->findByFactorySpeciesCode($params['factory_species_code']);

            $params['species_code'] = $factory_species->species->species_code;
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
                    $factory_species->species,
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
        }

        return view('plan.growth_simulation.add')->with(
            compact('params', 'factory', 'factory_species', 'harvesting_date', 'summary')
        );
    }

    /**
     * 生産シミュレーション 入力(検索)
     *
     * @param  \App\Http\Requests\Plan\AddSearchGrowthSimulationRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSearch(AddSearchGrowthSimulationRequest $request): RedirectResponse
    {
        $request->session()->put('plan.growth_simulation.add', $request->all());
        return redirect()->route('plan.growth_simulation.add');
    }

    /**
     * API用 生産シミュレーション登録（入力）
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        if ($request->ajax()) {
            $this->growth_simulation_service->createGrowthSimulation($request->all());
            return;
        }

        abort(404);
    }

    /**
     * 生産シミュレーション ロック
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lock(Request $request, GrowthSimulation $growth_simulation): RedirectResponse
    {
        try {
            $this->growth_simulation_service->lockGrowthSimulation($growth_simulation);
        } catch (OptimisticLockException $e) {
            return redirect()->route('plan.growth_simulation.index');
        }

        return redirect()->route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys());
    }

    /**
     * 生産シミュレーション ロック解除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unlock(Request $request, GrowthSimulation $growth_simulation): RedirectResponse
    {
        try {
            $this->growth_simulation_service->unlockGrowthSimulation($growth_simulation);
        } catch (OptimisticLockException $e) {
            // NOTE: nothing to do.
        }

        return redirect()->route('plan.growth_simulation.index');
    }

    /**
     * 生産シミュレーション 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, GrowthSimulation $growth_simulation)
    {
        if ($growth_simulation->canNotBeEdited()) {
            return redirect()->route('plan.growth_simulation.index');
        }

        $factory = $growth_simulation->factory_species->factory;
        $factory_species = $growth_simulation->factory_species;
        $harvesting_date = null;

        $params = $request->session()->get('plan.growth_simulation.edit', []);
        if (count($params) !== 0) {
            $params['factory_code'] = $factory->factory_code;
            $params['species_code'] = $factory_species->species->species_code;

            $harvesting_date = $params['display_term'] === 'date' ?
                HarvestingDate::parse($params['display_from_date']) :
                HarvestingDate::createFromYearMonth($params['display_from_month']);

            $panel_states = $this->panel_state_service
                ->getHarvestingStockQuantitiesBySpeciesAndHarvestingDate(
                    $params,
                    $harvesting_date,
                    $growth_simulation
                );

            $forecasted_product_rates = $this->crop_service
                ->getForecastedProductRatesBySpeciesAndHarvestingDate($params, $harvesting_date);

            $crops = $this->crop_service->getCropsBySpeciesAndHarvestingDate($params, $harvesting_date);

            $order_forecasts = $this->order_forecast_service
                ->getOrderForecastsBySpeciesAndHarvestingDate($params, $harvesting_date);

            $factory_products_with_order = $this->order_service
                ->summarizeOrdersPerFactoryProductAndDeliveryDestination(
                    $params,
                    $factory,
                    $factory_species->species,
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
        }

        return view('plan.growth_simulation.edit')->with(
            compact('growth_simulation', 'params', 'factory', 'factory_species', 'harvesting_date', 'summary')
        );
    }

    /**
     * 生産シミュレーション 修正(検索)
     *
     * @param  \App\Http\Requests\Plan\EditSearchGrowthSimulationRequest
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editSearch(
        EditSearchGrowthSimulationRequest $request,
        GrowthSimulation $growth_simulation
    ): RedirectResponse {
        $request->session()->put('plan.growth_simulation.edit', $request->all());
        return redirect()->route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys());
    }

    /**
     * 生産シミュレーション 修正(シミュレーション名変更)
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeName(Request $request, GrowthSimulation $growth_simulation): RedirectResponse
    {
        try {
            $this->growth_simulation_service->changeSimulationName($growth_simulation, $request->simulation_name);
        } catch (OptimisticLockException $e) {
            return redirect()->route('plan.growth_simulation.index');
        }

        return redirect()->route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys());
    }

    /**
     * API用 生産シミュレーション更新
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return void
     */
    public function update(Request $request, GrowthSimulation $growth_simulation): void
    {
        if ($request->ajax()) {
            $this->growth_simulation_service->updateGrowthSimulation($growth_simulation, $request->all());
            return;
        }

        abort(404);
    }

    /**
     * 生産シミュレーション 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, GrowthSimulation $growth_simulation): RedirectResponse
    {
        try {
            $this->growth_simulation_service->deleteGrowthSimulation($growth_simulation);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 残ベッドチェック
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\GrowthSimulation $growth_simulation
     * @return string
     */
    public function checkBedNumber(Request $request, GrowthSimulation $growth_simulation): string
    {
        if ($request->ajax()) {
            return json_encode($this->growth_simulation_service->checkBedNumber($growth_simulation));
        }

        abort(404);
    }

    /**
     * 生産シミュレーション 確定
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function fix(Request $request, GrowthSimulation $growth_simulation)
    {
        if ($request->ajax()) {
            if (! $growth_simulation->hasFixed()) {
                $this->growth_simulation_service->fixSimulation($growth_simulation);
            }

            return;
        }

        abort(404);
    }

    /**
     * 生産シミュレーション確定 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function indexFixed(Request $request)
    {
        $request->session()->forget('plan.growth_simulation.add');
        $request->session()->forget('plan.growth_simulation.edit');

        $growth_simulations = [];
        $params = $request->session()->get('plan.growth_simulation.fixed.search', []);

        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            $order = $request->only(['sort', 'order']);

            try {
                $growth_simulations = $this->growth_simulation_service
                    ->searchFixedGrowthSimulations($params, (int)$page, $order);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('plan.growth_simulation_fixed.index');
            }
        }

        return view('plan.growth_simulation.fixed')->with(compact('growth_simulations', 'params'));
    }

    /**
     * 生産シミュレーション確定 検索
     *
     * @param  \App\Http\Requests\Plan\SearchFixedGrowthSimulationRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function searchFixed(SearchFixedGrowthSimulationRequest $request): RedirectResponse
    {
        $request->session()->put('plan.growth_simulation.fixed.search', $request->all());
        return redirect()->route('plan.growth_simulation_fixed.index');
    }
}
