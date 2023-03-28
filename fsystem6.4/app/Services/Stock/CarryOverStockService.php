<?php

namespace App\Services\Stock;

use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use App\Models\Stock\CarryOverStock;
use App\Repositories\Stock\CarryOverStockRepository;
use App\Repositories\Stock\StockRepository;

class CarryOverStockService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @var \App\Repositories\Stock\CarryOverStockRepository
     */
    private $carry_over_stock_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @param  \App\Repositories\Stock\CarryOverStockRepository $carry_over_stock_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        StockRepository $stock_repo,
        CarryOverStockRepository $carry_over_stock_repo
    ) {
        $this->db = $db;
        $this->stock_repo = $stock_repo;
        $this->carry_over_stock_repo = $carry_over_stock_repo;
    }

    /**
     * 日次繰越在庫の登録
     *
     * @return void
     */
    public function saveCarryOveredStocks(): void
    {
        $this->db->transaction(function () {
            $carry_overed_stocks = $this->stock_repo->getCarryOveredStocksOnEndOfDay()
                ->map(function ($cos) {
                    return array_merge($cos->toArray(), [
                        'date' => Chronos::today()->subDay()->toDateString(),
                        'created_at' => Chronos::now(),
                        'created_by' => CarryOverStock::BATCH_USER,
                        'updated_at' => Chronos::now(),
                        'updated_by' => CarryOverStock::BATCH_USER
                    ]);
                });

            $this->carry_over_stock_repo->insertCarryOveredStocks($carry_overed_stocks);
        });
    }
}
