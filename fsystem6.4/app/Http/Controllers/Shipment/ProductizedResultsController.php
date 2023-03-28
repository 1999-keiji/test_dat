<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipment;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\DisposedStockException;
use App\Exceptions\MovedStockException;
use App\Exceptions\MultipleWarehouseStockException;
use App\Exceptions\OverAllocationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\SearchProductizedResultsRequest;
use App\Http\Requests\Shipment\SaveProductizedResultRequest;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Services\Master\FactoryService;
use App\Services\Shipment\ProductizedResultService;
use App\ValueObjects\Date\HarvestingDate;

class ProductizedResultsController extends Controller
{
    /**
     * @var \App\Services\Shipment\ProductizedResultService
     */
    private $productized_result_service;

    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param  \App\Services\Shipment\ProductizedResultService $productized_result_service
     * @param  \App\Services\Master\FactoryService $factory_service
     * @return void
     */
    public function __construct(ProductizedResultService $productized_result_service, FactoryService $factory_service)
    {
        parent::__construct();

        $this->productized_result_service = $productized_result_service;
        $this->factory_service = $factory_service;
    }

    /**
     * 製品化実績 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $productized_results = [];

        $params = $request->session()->get('shipment.productized_results.search', []);
        if (count($params) !== 0) {
            $productized_results = $this->productized_result_service
                ->searchProductizedResults($this->factory_service->find($params['factory_code']), $params);
        }

        return view('shipment.productized_results.index')->with(compact('productized_results', 'params'));
    }

    /**
     * 製品化実績 検索
     *
     * @param  \App\Http\Requests\Master\SearchProductizedResultsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchProductizedResultsRequest $request): RedirectResponse
    {
        $request->session()->put('shipment.productized_results.search', $request->all());
        return redirect()->route('shipment.productized_results.index');
    }

    /**
     * 製品化実績 入力
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \Illuminate\View\View
     */
    public function input(Request $request, Factory $factory, Species $species, HarvestingDate $harvesting_date): View
    {
        $productized_result = $this->productized_result_service
            ->getProductizedResult($factory, $species, $harvesting_date);

        $productized_result_details = $this->productized_result_service
            ->getProductizedResultDetails($factory, $species, $harvesting_date);

        $species_average_weight = $factory->factory_species
            ->filterBySpecies($species->species_code)
            ->getAverageWeight();

        return view('shipment.productized_results.input')->with(compact(
            'factory',
            'species',
            'harvesting_date',
            'productized_result',
            'productized_result_details',
            'species_average_weight'
        ));
    }

    /**
     * 製品化実績 保存
     *
     * @param \App\Http\Requests\Shipment\SaveProductizedResultRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function save(
        SaveProductizedResultRequest $request,
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): RedirectResponse {
        try {
            $this->productized_result_service->saveProductizedResult(
                $factory,
                $species,
                $harvesting_date,
                $request->productized_result,
                $request->productized_result_details ?: []
            );
        } catch (MultipleWarehouseStockException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['multiple']]);
        } catch (DisposedStockException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['disposed']]);
        } catch (MovedStockException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['moved']]);
        } catch (OverAllocationException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['reserved']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
