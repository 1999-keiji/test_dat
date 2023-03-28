<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\ExportStockHistoriesRequest;
use App\Services\Master\FactoryService;
use App\Services\Stock\StockHistoryService;

class StockHistoriesController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Stock\StockHistoryService
     */
    private $stock_history_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Stock\StockHistoryService $stock_history_service
     * @return void
     */
    public function __construct(FactoryService $factory_service, StockHistoryService $stock_history_service)
    {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->stock_history_service = $stock_history_service;
    }

    /**
     * 在庫履歴 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('stock.stock_histories.index');
    }

    /**
     * 在庫履歴 Excel出力
     *
     * @param \App\Http\Requests\Stock\ExportStockHistoriesRequest $request
     */
    public function export(ExportStockHistoriesRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $this->stock_history_service->exportStockHistories($request->all(), $factory);
    }
}
