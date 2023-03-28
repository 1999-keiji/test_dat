<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use App\Models\Stock\StockState;
use App\Models\Stock\Collections\StockStateCollection;

class StockStateRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Stock\StockState
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Stock\StockState $model
     * @return void
     */
    public function __construct(Connection $db, StockState $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 在庫状況の検索
     *
     * @param  array $params
     * @return \App\Models\Stock\Collections\StockStateCollection
     */
    public function searchStockStates(array $params): StockStateCollection
    {
        return $this->model
            ->select([
                'warehouse_abbreviation',
                'stock_status',
                'species_code',
                'species_abbreviation',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                $this->db->raw('(stock_quantity - disposal_quantity) AS stock_quantity'),
                $this->db->raw('((stock_quantity - disposal_quantity) * number_of_heads) AS stock_number'),
                $this->db->raw('(stock_weight - disposal_weight) AS stock_weight'),
                $this->db->raw("DATE_FORMAT(harvesting_date, '%Y/%m/%d') AS harvesting_date"),
                $this->db->raw("DATE_FORMAT(expiration_date, '%Y/%m/%d') AS expiration_date"),
                'delivery_destination_code',
                'delivery_destination_abbreviation',
                'number_of_cases',
                $this->db->raw("DATE_FORMAT(delivery_date, '%Y/%m/%d') AS delivery_date"),
                'delivery_lead_time'
            ])
            ->where('factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if (! is_null($params['warehouse_code'] ?? null)) {
                    $query->where('warehouse_code', $params['warehouse_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['species_code'] ?? null)) {
                    $query->where('species_code', $params['species_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['number_of_heads'] ?? null)) {
                    $query->where('number_of_heads', $params['number_of_heads']);
                }
                if (! is_null($params['weight_per_number_of_heads'] ?? null)) {
                    $query->where('weight_per_number_of_heads', $params['weight_per_number_of_heads']);
                }
                if (! is_null($params['input_group'] ?? null)) {
                    $query->where('input_group', $params['input_group']);
                }
            })
            ->where('stock_date', $params['stock_date'])
            ->where('stock_quantity', '>', 0)
            ->whereRaw('stock_quantity <> disposal_quantity')
            ->orderBy('species_code', 'ASC')
            ->orderBy('warehouse_code', 'ASC')
            ->orderBy('number_of_heads', 'ASC')
            ->orderBy('weight_per_number_of_heads', 'ASC')
            ->orderBy('input_group', 'ASC')
            ->orderBy('harvesting_date', 'ASC')
            ->orderBy('delivery_destination_code', 'ASC')
            ->orderBy('delivery_date', 'ASC')
            ->orderBy('stock_id', 'ASC')
            ->get();
    }

    /**
     * 在庫状況データの登録
     *
     * @param  \Illuminate\Support\Collection $stock_states
     * @return void
     */
    public function insertStockStates(Collection $stock_states): void
    {
        $stock_states->chunk(1000)->each(function ($chunked) {
            $this->model->insert($chunked->all());
        });
    }
}
