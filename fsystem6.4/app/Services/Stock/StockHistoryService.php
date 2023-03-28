<?php

namespace App\Services\Stock;

use Maatwebsite\Excel\Excel;
use App\Models\Master\Factory;
use App\Repositories\Stock\StockHistoryRepository;

class StockHistoryService
{
    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Stock\StockHistoryRepository
     */
    private $stock_history_repo;

    /**
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Stock\StockHistoryRepository $stock_history_repo
     * @return void
     */
    public function __construct(Excel $excel, StockHistoryRepository $stock_history_repo)
    {
        $this->excel = $excel;
        $this->stock_history_repo = $stock_history_repo;
    }

    /**
     * 在庫履歴一覧 Excel出力
     *
     * @param array $params
     * @param \App\Models\Master\Factory $factory
     */
    public function exportStockHistories(array $params, Factory $factory)
    {
        $stock_histories = $this->stock_history_repo->searchStockHistories($params);

        $file_name = generate_file_name(trans('view.stock.stock_histories.index'));
        $this->excel->create($file_name, function ($excel) use ($params, $factory, $stock_histories) {
            $sheet_name = trans('view.stock.stock_histories.index');
            $excel->sheet($sheet_name, function ($sheet) use ($params, $stock_histories, $factory) {
                $sheet->loadView('stock.stock_histories.export')->with(compact('params', 'factory', 'stock_histories'));
            });
        })
            ->export();
    }
}
