<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Stock\StockHistory;
use App\ValueObjects\Date\WorkingDate;

class StockHistoryRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Stock\StockHistory
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Stock\StockHistory $model
     * @return void
     */
    public function __construct(Connection $db, StockHistory $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 在庫履歴一覧 検索
     *
     * @param  array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchStockHistories(array $params): Collection
    {
        return $this->model
            ->select([
                'stock_histories.created_at',
                'stock_histories.screen',
                'warehouses.warehouse_abbreviation',
                'stock_histories.stock_status',
                'species.species_name',
                'stock_histories.number_of_heads',
                'stock_histories.weight_per_number_of_heads',
                'stock_histories.input_group',
                $this->db->raw("DATE_FORMAT(stock_histories.harvesting_date, '%Y/%m/%d') AS harvesting_date"),
                $this->db->raw("DATE_FORMAT(stock_histories.expiration_date, '%Y/%m/%d') AS expiration_date"),
                $this->db->raw("DATE_FORMAT(stock_histories.delivery_date, '%Y/%m/%d') AS delivery_date"),
                'stock_histories.allocation_flag',
                'delivery_destinations.delivery_destination_abbreviation',
                'stock_histories.delivery_lead_time',
                'stock_histories.stock_quantity',
                'stock_histories.transistion_quantity',
                'users.user_name',
            ])
            ->join('warehouses', 'stock_histories.warehouse_code', '=', 'warehouses.warehouse_code')
            ->join('species', 'stock_histories.species_code', '=', 'species.species_code')
            ->leftJoin(
                'delivery_destinations',
                'stock_histories.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->join('users', 'users.user_code', '=', 'stock_histories.created_by')
            ->where('stock_histories.factory_code', $params['factory_code'])
            ->where(function ($query) use ($params) {
                if (! is_null($params['warehouse_code'] ?? null)) {
                    $query->where('stock_histories.warehouse_code', $params['warehouse_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['species_code'] ?? null)) {
                    $query->where('stock_histories.species_code', $params['species_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['number_of_heads'] ?? null)) {
                    $query->where('stock_histories.number_of_heads', $params['number_of_heads']);
                }
                if (! is_null($params['weight_per_number_of_heads'] ?? null)) {
                    $query->where('stock_histories.weight_per_number_of_heads', $params['weight_per_number_of_heads']);
                }
                if (! is_null($params['input_group'] ?? null)) {
                    $query->where('stock_histories.input_group', $params['input_group']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['screen'] ?? null)) {
                    $query->where('stock_histories.screen', $params['screen']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['harvesting_date_from'] ?? null)) {
                    $query->where('stock_histories.harvesting_date', '>=', $params['harvesting_date_from']);
                }
                if (! is_null($params['harvesting_date_to'] ?? null)) {
                    $query->where('stock_histories.harvesting_date', '<=', $params['harvesting_date_to']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['user_code'] ?? null)) {
                    $query->where('stock_histories.created_by', $params['user_code']);
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['working_date_from'] ?? null)) {
                    $query->where(
                        'stock_histories.created_at',
                        '>=',
                        WorkingDate::parse($params['working_date_from'])->startOfDay()->format('Y-m-d H:i:s')
                    );
                }
                if (! is_null($params['working_date_to'] ?? null)) {
                    $query->where(
                        'stock_histories.created_at',
                        '<=',
                        WorkingDate::parse($params['working_date_to'])->endOfDay()->format('Y-m-d H:i:s')
                    );
                }
            })
            ->where(function ($query) use ($params) {
                if (! is_null($params['delivery_date_from'] ?? null)) {
                    $query->where('stock_histories.delivery_date', '>=', $params['delivery_date_from']);
                }
                if (! is_null($params['delivery_date_to'] ?? null)) {
                    $query->where('stock_histories.delivery_date', '<=', $params['delivery_date_to']);
                }
            })
            ->get();
    }
}
