<?php

namespace App\Http\Controllers\Stock;

use InvalidArgumentException;
use PDOException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\AdjustStockRequest;
use App\Http\Requests\Stock\DisposeStocksRequest;
use App\Http\Requests\Stock\MoveStockRequest;
use App\Http\Requests\Stock\SearchStocksRequest;
use App\Http\Requests\Stock\SearchDisposedStocksRequest;
use App\Http\Requests\Stock\SearchStockSummaryRequest;
use App\Models\Stock\Stock;
use App\Services\Master\FactoryService;
use App\Services\Stock\StockService;

class StocksController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Stock\StockService
     */
    private $stock_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Stock\StockService $stock_service
     * @return void
     */
    public function __construct(FactoryService $factory_service, StockService $stock_service)
    {
        parent::__construct();
        $this->factory_service = $factory_service;
        $this->stock_service = $stock_service;
    }

    /**
     * 在庫サマリー 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function summary(Request $request): View
    {
        $stock_summary_list = [];

        $params = $request->session()->get('stock.stocks.summary.search', []);
        if (count($params) !== 0) {
            $order = $request->only(['sort', 'order']);
            $stock_summary_list = $this->stock_service->searchStockSummary($params, $order);
        }

        return view('stock.stocks.summary')->with(compact('stock_summary_list', 'params'));
    }

    /**
     * 在庫サマリー 検索
     *
     * @param  \App\Http\Requests\Stock\SearchStockSummaryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function searchSummary(SearchStockSummaryRequest $request): RedirectResponse
    {
        $request->session()->put('stock.stocks.summary.search', $request->all());
        return redirect()->route('stock.stocks.summary.index');
    }

    /**
     * 在庫一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $stocks = [];

        $params = $request->session()->get('stock.stocks.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            $order = $request->only(['sort', 'order']);

            try {
                $stocks = $this->stock_service->searchStocks($params, (int)$page, $order);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('stock.stocks.index');
            }
        }

        return view('stock.stocks.index')->with(compact('stocks', 'params'));
    }

    /**
     * 在庫一覧 検索
     *
     * @param  \App\Http\Requests\Stock\SearchStocksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchStocksRequest $request): RedirectResponse
    {
        $request->session()->put('stock.stocks.search', $request->all());
        return redirect()->route('stock.stocks.index');
    }

    /**
     * 在庫一覧 出力
     *
     * @param  \App\Http\Requests\Stock\SearchStocksRequest $request
     */
    public function export(SearchStocksRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $this->stock_service->exportStocks($request->all(), $factory);
    }

    /**
     * 在庫移動
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Stock\Stock $stock
     * @return \Illuminate\View\View
     */
    public function move(Request $request, Stock $stock): View
    {
        return view('stock.stocks.move')->with(compact('stock'));
    }

    /**
     * 在庫移動 保存
     *
     * @param  \App\Http\Requests\MoveStockRequest $request
     * @param  \App\Models\Stock\Stock $stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveMoving(MoveStockRequest $request, Stock $stock): RedirectResponse
    {
        try {
            $stock = $this->stock_service->moveStock($stock, $request->except('moving_lead_time'));
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('stock.stocks.move', $stock->stock_id)
            ->with(['alert' => $this->operations['success'], 'stock.stocks.move.export_file' => true]);
    }

    /**
     * 在庫移動 帳票出力
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Stock\Stock $stock
     */
    public function exportMoved(Request $request, Stock $stock)
    {
        $this->stock_service->exportMovedStock($stock);
    }

    /**
     * 在庫調整
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Stock\Stock $stock
     * @return \Illuminate\View\View
     */
    public function adjust(Request $request, Stock $stock): View
    {
        return view('stock.stocks.adjust')->with(compact('stock'));
    }

    /**
     * 在庫調整 保存
     *
     * @param  \App\Http\Requests\AdjustStockRequest $request
     * @param  \App\Models\Stock\Stock $stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveAdjusting(AdjustStockRequest $request, Stock $stock): RedirectResponse
    {
        try {
            $stock = $this->stock_service->adjustStock($stock, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('stock.stocks.adjust', $stock->stock_id)
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 廃棄登録
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function dispose(Request $request): View
    {
        $species_list = [];

        $params = $request->session()->get('stock.stocks.dispose.search', []);
        if (count($params) !== 0) {
            $species_list = $this->stock_service->searchDisposedStocks($params);
        }

        return view('stock.stocks.dispose')->with(compact('species_list', 'params'));
    }

    /**
     * 廃棄登録 検索
     *
     * @param  \App\Http\Requests\Stock\SearchDisposedStocksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function searchDisposed(SearchDisposedStocksRequest $request): RedirectResponse
    {
        $request->session()->put('stock.stocks.dispose.search', $request->all());
        return redirect()->route('stock.stocks.dispose.index');
    }

    /**
     * 廃棄登録 保存
     *
     * @param  \App\Http\Requests\Stock\DisposeStocksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveDisposing(DisposeStocksRequest $request): RedirectResponse
    {
        try {
            $this->stock_service->disposeStocks($request->stocks);
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('stock.stocks.dispose.index')->with(['alert' => $this->operations['success']]);
    }

    /**
     * 廃棄登録 出力
     *
     * @param \App\Http\Requests\Stock\SearchDisposedStocksRequest $request
     */
    public function exportDisposed(SearchDisposedStocksRequest $request)
    {
        try {
            $factory = $this->factory_service->find($request->factory_code);
            $this->stock_service->exportDisposedStocks($request->all(), $factory);
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['not_found']]);
        }
    }
}
