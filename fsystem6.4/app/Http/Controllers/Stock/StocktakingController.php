<?php

namespace App\Http\Controllers\Stock;

use InvalidArgumentException;
use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\SearchStocktakingRequest;
use App\Models\Stock\Stocktaking;
use App\Services\Stock\StockService;
use App\Services\Stock\StocktakingService;

class StocktakingController extends Controller
{
    /**
     * @var \App\Services\Stock\StockService
     */
    private $stock_service;

    /**
     * @var \App\Services\Stock\StocktakingService
     */
    private $stocktaking_service;

    /**
     * @param  \App\Services\Stock\StockService $stock_service
     * @param  \App\Services\Stock\StocktakingService $stocktaking_service
     * @return void
     */
    public function __construct(StockService $stock_service, StocktakingService $stocktaking_service)
    {
        parent::__construct();

        $this->stock_service = $stock_service;
        $this->stocktaking_service = $stocktaking_service;
    }

    /**
     * 在庫棚卸 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $stocktaking = null;
        $stocktaking_details = [];

        $params = $request->session()->get('stock.stocktaking.search', []);
        if (count($params) !== 0) {
            $stocktaking = $this->stocktaking_service->getStocktaking($params);
            $stocktaking_details = $this->stocktaking_service
                ->getStocktakingDetails($stocktaking, $request->only(['sort', 'order']));
        }

        return view('stock.stocktaking.index')->with(compact('stocktaking', 'stocktaking_details', 'params'));
    }

    /**
     * 在庫棚卸 検索
     *
     * @param  \App\Http\Requests\Stock\SearchStocktakingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchStocktakingRequest $request): RedirectResponse
    {
        $moving_stocks = $this->stock_service->searchMovingStocks();
        if ($moving_stocks->isNotEmpty()) {
            return redirect()->back()->with(['alert' => $this->operations['moving']]);
        }

        $request->session()->put('stock.stocktaking.search', $request->all());
        return redirect()->route('stock.stocktaking.index');
    }

    /**
     * 在庫棚卸 開始
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start(Request $request): RedirectResponse
    {
        $params = $request->session()->get('stock.stocktaking.search', []);
        if (count($params) === 0) {
            return redirect()->route('stock.stocktaking.index');
        }

        $stocktaking = $this->stocktaking_service->getStocktaking($params);
        if (! $stocktaking->hasNotStartedYet()) {
            return redirect()->back()->with(['alert' => $this->operations['interuptted']]);
        }

        try {
            $this->stocktaking_service->startStocktaking($stocktaking);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('stock.stocktaking.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 在庫棚卸 やり直し
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh(Request $request, Stocktaking $stocktaking): RedirectResponse
    {
        try {
            $this->stocktaking_service->refreshStocktaking($stocktaking);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('stock.stocktaking.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 在庫棚卸 一時保存
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function keep(Request $request, Stocktaking $stocktaking): RedirectResponse
    {
        try {
            $this->stocktaking_service->keepStocktaking($stocktaking, $request->stocktaking_details ?: []);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('stock.stocktaking.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 在庫棚卸 再開
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restart(Request $request, Stocktaking $stocktaking): RedirectResponse
    {
        $this->stocktaking_service->restartStocktaking($stocktaking);
        return redirect()->route('stock.stocktaking.index');
    }

    /**
     * 在庫棚卸 完了
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(Request $request, Stocktaking $stocktaking): RedirectResponse
    {
        try {
            $this->stocktaking_service->completeStocktaking($stocktaking, $request->stocktaking_details ?: []);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('stock.stocktaking.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 在庫棚卸 棚卸表出力
     *
     * @param \Illuminate\Http\Request $request
     */
    public function export(Request $request)
    {
        $params = $request->session()->get('stock.stocktaking.search', []);
        if (count($params) === 0) {
            return redirect()->route('stock.stocktaking.index');
        }

        $stocktaking = $this->stocktaking_service->getStocktaking($params);
        $this->stocktaking_service->exportStocktaking($stocktaking);
    }

    /**
     * 在庫棚卸 月末遷移表出力
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Stock\Stocktaking $stocktaking
     */
    public function exportTransition(Request $request, Stocktaking $stocktaking)
    {
        $params = $request->session()->get('stock.stocktaking.search', []);
        if (count($params) === 0) {
            return redirect()->route('stock.stocktaking.index');
        }

        if (! $stocktaking->hasCompleted()) {
            return redirect()->route('stock.stocktaking.index');
        }

        try {
            $this->stocktaking_service->exportTransition($stocktaking);
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }
    }
}
