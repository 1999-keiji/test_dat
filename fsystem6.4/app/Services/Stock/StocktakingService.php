<?php

namespace App\Services\Stock;

use InvalidArgumentException;
use Illuminate\Database\Connection;
use Maatwebsite\Excel\Excel;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;
use App\Models\Stock\Stocktaking;
use App\Models\Stock\Collections\StocktakingDetailCollection;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Shipment\ProductAllocationRepository;
use App\Repositories\Stock\StockManipulationControlRepository;
use App\Repositories\Stock\StockResultByWarehouseRepository;
use App\Repositories\Stock\StocktakingRepository;
use App\Repositories\Stock\StocktakingDetailRepository;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Date\WorkingDate;

class StocktakingService
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
     * @var \App\Repositories\Stock\StocktakingRepository
     */
    private $stocktaking_repo;

    /**
     * @var \App\Repositories\Stock\StocktakingDetailRepository
     */
    private $stocktaking_detail_repo;

    /**
     * @var \App\Repositories\Stock\StockManipulationControlRepository
     */
    private $stock_manipulation_control_repo;

    /**
     * @var \App\Repositories\Stock\StockResultByWarehouseRepository
     */
    private $stock_result_by_warehouse_repo;

    /**
     * @var \App\Repositories\Shipment\ProductAllocationRepository
     */
    private $product_allocation_repo;

    /**
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Stock\StocktakingRepository $stocktaking_repo
     * @param  \App\Repositories\Stock\StocktakingDetailRepository $stocktaking_detail_repo
     * @param  \App\Repositories\Stock\StockManipulationControlRepository $stock_manipulation_control_repo
     * @param  \App\Repositories\Stock\StockResultByWarehouseRepository $stock_result_by_warehouse_repo
     * @param  \App\Repositories\Shipment\ProductAllocationRepository $product_allocation_repo
     * @param  \App\Repositories\Order\OrderRepository $order_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        StocktakingRepository $stocktaking_repo,
        StocktakingDetailRepository $stocktaking_detail_repo,
        StockManipulationControlRepository $stock_manipulation_control_repo,
        StockResultByWarehouseRepository $stock_result_by_warehouse_repo,
        ProductAllocationRepository $product_allocation_repo,
        OrderRepository $order_repo
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->stocktaking_repo = $stocktaking_repo;
        $this->stocktaking_detail_repo = $stocktaking_detail_repo;
        $this->stock_manipulation_control_repo = $stock_manipulation_control_repo;
        $this->stock_result_by_warehouse_repo = $stock_result_by_warehouse_repo;
        $this->product_allocation_repo = $product_allocation_repo;
        $this->order_repo = $order_repo;
    }

    /**
     * 在庫棚卸データの取得
     *
     * @param  array $params
     * @return \App\Models\Stock\Stocktaking
     */
    public function getStocktaking(array $params): Stocktaking
    {
        $stocktaking = $this->stocktaking_repo->getStocktaking($params);
        if (is_null($stocktaking)) {
            $stocktaking = new Stocktaking();
            $stocktaking->fill($params);
        }

        return $stocktaking;
    }

    /**
     * 在庫棚卸明細データの取得
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $order
     * @return \App\Models\Stock\Collections\StocktakingDetailCollection
     */
    public function getStocktakingDetails(Stocktaking $stocktaking, array $order = []): StocktakingDetailCollection
    {
        return $stocktaking->hasCompleted() ?
            $this->stocktaking_detail_repo->getFixedStocktakingDetails($stocktaking, $order):
            $this->stocktaking_detail_repo->getStocktakingDetails($stocktaking, $order);
    }

    /**
     * 在庫棚卸処理の開始
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return void
     */
    public function startStocktaking(Stocktaking $stocktaking): void
    {
        $this->db->transaction(function () use ($stocktaking) {
            $this->stock_manipulation_control_repo->lockStockManipulation($stocktaking->factory_code);

            $stocktaking->save();
            $this->getStocktakingDetails($stocktaking)->each(function ($sd) {
                $this->stocktaking_detail_repo->create($sd->toArray());
            });
        });
    }

    /**
     * 在庫棚卸処理のやり直し
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return void
     */
    public function refreshStocktaking(Stocktaking $stocktaking): void
    {
        $this->db->transaction(function () use ($stocktaking) {
            $stocktaking->stocktaking_details->each(function ($sd) {
                $sd->delete();
            });

            $stocktaking->delete();
            $this->stock_manipulation_control_repo->unlockStockManipulation($stocktaking->factory_code);
        });
    }

    /**
     * 在庫棚卸明細データの保存
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $stocktaking_details
     * @return void
     */
    public function saveStocktakingDetails(Stocktaking $stocktaking, array $stocktaking_details)
    {
        foreach ($stocktaking_details as $sd) {
            $params = array_merge([
                'factory_code' => $stocktaking->factory_code,
                'warehouse_code' => $stocktaking->warehouse_code,
                'stocktaking_month' => $stocktaking->stocktaking_month
            ], $sd);

            $stocktaking_detail = $this->stocktaking_detail_repo->getStocktakingDetail($params);
            if (is_null($stocktaking_detail)) {
                $this->stocktaking_detail_repo->create(array_merge($params, [
                    'stock_quantity' => 0,
                    'remark' => $params['remark'] ?: '',
                    'unit' => $params['unit'] ?: ''
                ]));
            }
            if (! is_null($stocktaking_detail)) {
                $this->stocktaking_detail_repo->update($stocktaking_detail, [
                    'actual_stock_quantity' => $params['actual_stock_quantity'],
                    'remark' => $params['remark'] ?: ''
                ]);
            }
        }
    }

    /**
     * 在庫棚卸処理の一時保存
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $stocktaking_details
     * @return void
     */
    public function keepStocktaking(Stocktaking $stocktaking, array $stocktaking_details): void
    {
        $this->db->transaction(function () use ($stocktaking, $stocktaking_details) {
            $this->saveStocktakingDetails($stocktaking, $stocktaking_details);
            $this->stock_manipulation_control_repo->unlockStockManipulation($stocktaking->factory_code);
        });
    }

    /**
     * 在庫棚卸処理の再開
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @return void
     */
    public function restartStocktaking(Stocktaking $stocktaking): void
    {
        $this->stock_manipulation_control_repo->lockStockManipulation($stocktaking->factory_code);
    }

    /**
     * 在庫棚卸処理の完了
     *
     * @param  \App\Models\Stock\Stocktaking $stocktaking
     * @param  array $stocktaking_details
     * @return void
     */
    public function completeStocktaking(Stocktaking $stocktaking, array $stocktaking_details): void
    {
        $this->db->transaction(function () use ($stocktaking, $stocktaking_details) {
            $this->saveStocktakingDetails($stocktaking, $stocktaking_details);

            $stocktaking->stocktaking_comp_at = Chronos::now();
            $stocktaking->save();

            $this->stock_manipulation_control_repo->unlockStockManipulation($stocktaking->factory_code);
        });
    }

    /**
     * 在庫棚卸表のダウンロード
     *
     * @param \App\Models\Stock\Stocktaking $stocktaking
     * @param array $params
     */
    public function exportStocktaking(Stocktaking $stocktaking)
    {
        $stocktaking_details = $this->getStocktakingDetails($stocktaking);

        $file_name = generate_file_name(trans('view.stock.stocktaking.stocktaking_file_name'));
        $this->excel->create($file_name, function ($excel) use ($stocktaking, $stocktaking_details) {
            $sheet_name = trans('view.stock.stocktaking.stocktaking_file_name');
            $excel->sheet($sheet_name, function ($sheet) use ($stocktaking, $stocktaking_details) {
                $sheet->setFontFamily('MS PGothic');
                $sheet->getSheetView()->setZoomScale(85);
                $sheet->setAutoSize(false);

                $sheet->loadView('stock.stocktaking.export')->with(compact('stocktaking', 'stocktaking_details'));
                $sheet->mergeCells('J2:J3');
                $sheet->mergeCells('K2:K3');
                $sheet->mergeCells('L2:L3');
                $sheet->mergeCells('J4:J8');
                $sheet->mergeCells('K4:K8');
                $sheet->mergeCells('L4:L8');
            });
        })
            ->export();
    }

    /**
     * 月末遷移表のダウンロード
     *
     * @param \App\Models\Stock\Stocktaking $stocktaking
     */
    public function exportTransition(Stocktaking $stocktaking)
    {
        $start_date = $stocktaking->getStocktakingCompletedAt()->addDay()->startOfDay();

        $base_dates = [];
        while ($stocktaking->getStocktakingCompletedAt()->endOfMonth()->gt($start_date)) {
            $base_dates[] = $start_date;
            $start_date = $start_date->addDay();
        }

        if (count($base_dates) === 0) {
            throw new InvalidArgumentException('stocktaked at end of month.');
        }

        $species_list = $this->stocktaking_detail_repo
            ->getStocktakingDetailsGroupByStockStyle($stocktaking, $base_dates)
            ->groupBySpecies()
            ->map(function ($species) use ($base_dates) {
                $species->stock_styles = $species->stock_styles
                    ->map(function ($ss) use ($base_dates) {
                        $producted_stocks = null;
                        if (! $ss->hasAllocated()) {
                            $producted_stocks = $this->stock_result_by_warehouse_repo
                                ->getProductedStocks($ss, $base_dates);
                        }

                        $allocated_stocks = $this->product_allocation_repo
                            ->getAllocatedStocksPerStockStyle($ss, $base_dates);

                        $shipped_stocks = null;
                        if ($ss->hasAllocated()) {
                            $shipped_stocks = $this->order_repo->getShippedStocksPerStockStyle($ss, $base_dates);
                        }

                        $dates = [];
                        foreach ($base_dates as $d) {
                            $allocated_date = WorkingDate::parse($d->toDateString());
                            $quantities = [
                                'producted' => 0,
                                'allocated' => $allocated_stocks
                                    ->filterByAllocatedDate($allocated_date)
                                    ->allocation_quantity ?? 0,
                                'shipped' => 0
                            ];

                            if (! is_null($producted_stocks)) {
                                $harvesting_date = HarvestingDate::parse($d->toDateString());
                                $quantities['producted'] = $producted_stocks
                                    ->filterByHarvestingDate($harvesting_date)
                                    ->stock_quantity ?? 0;
                            }

                            if (! $ss->hasAllocated()) {
                                $quantities['allocated'] = $quantities['allocated'] * -1;
                            }

                            if (! is_null($shipped_stocks)) {
                                $shipping_date = ShippingDate::parse($d->toDateString());
                                $quantities['shipped'] = ($shipped_stocks
                                    ->filterByShippingDate($shipping_date)
                                    ->first()
                                    ->shipped_quantity ?? 0) * -1;
                            }

                            $dates[$d->format('Ymd')] = (object)$quantities;
                        }

                        $ss->dates = $dates;
                        $ss->current_stock_quantity = $ss->actual_stock_quantity +
                            collect($ss->dates)->sum('producted') +
                            collect($ss->dates)->sum('allocated') +
                            collect($ss->dates)->sum('shipped');

                        return $ss;
                    });

                return $species;
            });

        $file_name = generate_file_name(trans('view.stock.stocktaking.transition_file'));
        $this->excel->create($file_name, function ($excel) use ($stocktaking, $base_dates, $species_list) {
            $sheet_name = trans('view.stock.stocktaking.transition_file');
            $excel->sheet($sheet_name, function ($sheet) use ($stocktaking, $base_dates, $species_list) {
                $sheet->setFontFamily('MS PGothic');
                $sheet->setAutoSize(false);

                $sheet->loadView('stock.stocktaking.export_transition_file')
                    ->with(compact('stocktaking', 'base_dates', 'species_list'));
            });
        })
            ->export();
    }
}
