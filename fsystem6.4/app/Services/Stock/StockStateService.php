<?php

namespace App\Services\Stock;

use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use Maatwebsite\Excel\Excel;
use App\Models\Master\Factory;
use App\Models\Stock\StockState;
use App\Repositories\Stock\StockRepository;
use App\Repositories\Stock\StockStateRepository;

class StockStateService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @var \App\Repositories\Stock\StockStateRepository
     */
    private $stock_state_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @param  \App\Repositories\Stock\StockStateRepository $stock_state_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        StockRepository $stock_repo,
        StockStateRepository $stock_state_repo
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->stock_repo = $stock_repo;
        $this->stock_state_repo = $stock_state_repo;
    }

    /**
     * 在庫状況 Excel出力
     *
     * @param array $params
     * @param \App\Models\Master\Factory $factory
     */
    public function exportStockStates(array $params, Factory $factory)
    {
        $stock_states = $params['export_type'] === 'current' ?
            $this->stock_repo->searchStockStates($params) :
            $this->stock_state_repo->searchStockStates($params);

        $file_name = generate_file_name(trans('view.stock.stock_states.index'));
        $this->excel->create($file_name, function ($excel) use ($params, $factory, $stock_states) {
            $sheet_name = trans('view.stock.stock_states.index');
            $excel->sheet($sheet_name, function ($sheet) use ($params, $factory, $stock_states) {
                $sheet->loadView('stock.stock_states.export')->with(compact('params', 'factory', 'stock_states'));
                $sheet->getStyle('A2:P2')->getAlignment()->setWrapText(true);
            });
        })
            ->export();
    }

    /**
     * 日次在庫状況の登録
     *
     * @return void
     */
    public function saveStockStates(): void
    {
        $this->db->transaction(function () {
            $stock_states = $this->stock_repo->searchStockStates([], Chronos::today()->subDay()->toDateString())
                ->map(function ($ss) {
                    return array_merge(array_except($ss->toArray(), [
                        'original_stock_quantity',
                        'original_stock_number',
                        'original_stock_weight',
                        'order_number'
                    ]), [
                        'stock_date' => Chronos::today()->subDay()->toDateString(),
                        'stock_quantity' => $ss->original_stock_quantity,
                        'stock_number' => $ss->original_stock_number,
                        'stock_weight' => $ss->original_stock_weight,
                        'expiration_date' => $ss->getExpiredOn()->toDateString(),
                        'created_at' => Chronos::now(),
                        'created_by' => StockState::BATCH_USER,
                        'updated_at' => Chronos::now(),
                        'updated_by' => StockState::BATCH_USER
                    ]);
                });

            $this->stock_state_repo->insertStockStates($stock_states);
        });
    }
}
