<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\ExportStockStatesRequest;
use App\Services\Master\FactoryService;
use App\Services\Stock\StockStateService;

class StockStatesController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Stock\StockStateService
     */
    private $stock_state_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Stock\StockStateService $stock_state_service
     * @return void
     */
    public function __construct(FactoryService $factory_service, StockStateService $stock_state_service)
    {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->stock_state_service = $stock_state_service;
    }

    /**
     * 在庫状況確認
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('stock.stock_states.index');
    }

    /**
     * 在庫状況確認 ファイル出力
     *
     * @param \App\Http\Requests\Stock\ExportStockStatesRequest $request
     */
    public function export(ExportStockStatesRequest $request)
    {
        $factory = $this->factory_service->find($request->factory_code);
        $this->stock_state_service->exportStockStates($request->all(), $factory);
    }
}
