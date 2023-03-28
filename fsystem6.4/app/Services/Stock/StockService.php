<?php

namespace App\Services\Stock;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Connection;
use Maatwebsite\Excel\Excel;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Master\Warehouse;
use App\Models\Stock\Stock;
use App\Models\Stock\Collections\StockCollection;
use App\Repositories\Stock\StockRepository;
use App\Repositories\Stock\StockResultByWarehouseRepository;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\StockStatus;

class StockService
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
     * @var \App\Repositories\Shipment\StockResultByWarehouseRepository
     */
    private $stock_result_by_warehouse_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Stock\StockRepository $stock_repo
     * @param  \App\Repositories\Stock\StockResultByWarehouseRepository $stock_result_by_warehouse_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        StockRepository $stock_repo,
        StockResultByWarehouseRepository $stock_result_by_warehouse_repo
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->stock_repo = $stock_repo;
        $this->stock_result_by_warehouse_repo = $stock_result_by_warehouse_repo;
    }

    /**
     * 在庫サマリーの検索
     *
     * @param  array $params
     * @param  array $order
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function searchStockSummary(array $params, array $order): StockCollection
    {
        return $this->stock_repo->searchStockSummary($params, $order);
    }

    /**
     * 在庫データの検索
     *
     * @param  array $params
     * @param  int
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Stock\Collections\StockCollection
     * @throws \App\Exceptions\PageOverException
     */
    public function searchStocks(array $params, int $page, array $order)
    {
        $stocks = $this->stock_repo->searchStocks($params, $order, true);
        if ($page > $stocks->lastPage() && $stocks->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $stocks;
    }

    /**
     * 在庫一覧 Excel出力
     *
     * @param array $params
     * @param \App\Models\Master\Factory $factory
     */
    public function exportStocks(array $params, Factory $factory)
    {
        $stocks = $this->stock_repo->searchStocks($params);

        $file_name = generate_file_name(trans('view.stock.stocks.index'));
        $this->excel->create($file_name, function ($excel) use ($params, $factory, $stocks) {
            $excel->sheet(trans('view.stock.stocks.index'), function ($sheet) use ($stocks, $factory) {
                $sheet->loadView('stock.stocks.export')->with(compact('factory', 'stocks'));
            });

            $excel->sheet(trans('view.global.export_condition'), function ($sheet) use ($params, $factory, $stocks) {
                $sheet->loadView('stock.stocks.export_output_condition')->with(compact('params', 'factory', 'stocks'));
            });

            $excel->setActiveSheetIndex(0);
        })
        ->export();
    }

    /**
     * 在庫移動
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  array $params
     * @return array \App\Models\Stock\Stock
     */
    public function moveStock(Stock $stock, array $params): Stock
    {
        return $this->db->transaction(function () use ($stock, $params) {
            if ($stock->hasAllocated()) {
                $message = 'the stock was already allocated. stock_id: %d';
                throw new OptimisticLockException(sprintf($message, $stock->stock_id));
            }

            $stock_quantity = (int)$params['stock_quantity'];

            $srbw = $stock->stock_result_by_warehouse;
            $stock = ($stock->stock_quantity === $stock_quantity) ?
                $this->stock_repo->moveStockWholly($stock, $params) :
                $this->stock_repo->moveStockPartially($stock, $params);

            $srbw->adjustment_quantity = $srbw->adjustment_quantity + ($stock_quantity * -1);
            $srbw->save();

            $srbw = $stock->stock_result_by_warehouse;
            if (is_null($srbw)) {
                $this->stock_result_by_warehouse_repo->createMovedStock($stock);
            }
            if (! is_null($srbw)) {
                $srbw->adjustment_quantity = $srbw->adjustment_quantity + $stock_quantity;
                $srbw->save();
            }

            return $stock;
        });
    }

    /**
     * 倉庫間移動指示書の出力
     *
     * @param \App\Models\Stock\Stock $stock
     */
    public function exportMovedStock(Stock $stock)
    {
        $file_name = generate_file_name(trans('view.stock.stocks.moved_stock_file'));
        $this->excel->create($file_name, function ($excel) use ($stock) {
            $excel->sheet(trans('view.stock.stocks.moved_stock_file'), function ($sheet) use ($stock) {
                $sheet->setFontFamily('MS PGothic');
                $sheet->getSheetView()->setZoomScale(90);
                $sheet->setAutoSize(false);
                $sheet->getDefaultColumnDimension()->setWidth(3);
                $sheet->getDefaultRowDimension()->setRowHeight(14);
                $sheet->cells('A1:AZ44', function ($cells) {
                    $cells->setBackground('FFFFFF');
                });

                $sheet->loadView('stock.stocks.export_moved_stock', compact('stock'));
                $sheet->mergeCells('AS4:AX5');
                $sheet->mergeCells('S6:AH9');
                $sheet->mergeCells('C11:Q12');
                $sheet->mergeCells('C19:K20');
                $sheet->mergeCells('L19:W20');
                $sheet->mergeCells('X19:AA20');
                $sheet->mergeCells('AB19:AE20');
                $sheet->mergeCells('AF19:AX20');
                $sheet->mergeCells('C21:K22');
                $sheet->mergeCells('L21:W22');
                $sheet->mergeCells('X21:AA22');
                $sheet->mergeCells('AB21:AE22');
                $sheet->mergeCells('AF21:AX22');
            });
        })
            ->export();
    }

    /**
     * 在庫調整
     *
     * @param  \App\Models\Stock\Stock $stock
     * @param  array $params
     * @return \App\Models\Stock\Stock $stock
     */
    public function adjustStock(Stock $stock, array $params): Stock
    {
        return $this->db->transaction(function () use ($stock, $params) {
            if ($stock->hasAllocated()) {
                $message = 'the stock was already allocated. stock_id: %d';
                throw new OptimisticLockException(sprintf($message, $stock->stock_id));
            }

            if ($params['adjusting_type'] === 'replace') {
                $srbw = $stock->stock_result_by_warehouse;
                $stock_quantity = $stock->stock_quantity;

                $replaced_stock = $this->stock_repo->replaceStock($stock, $params);
                $srbw->adjustment_quantity = $srbw->adjustment_quantity + ($stock_quantity * -1);
                $srbw->save();

                $srbw = $replaced_stock->stock_result_by_warehouse;
                if (is_null($srbw)) {
                    $this->stock_result_by_warehouse_repo->createMovedStock($replaced_stock);
                }
                if (! is_null($srbw)) {
                    $srbw->adjustment_quantity = $srbw->adjustment_quantity + $params['stock_quantity'];
                    $srbw->save();
                }

                return $replaced_stock;
            }

            if ($params['adjusting_type'] === 'separate') {
                return $this->stock_repo->separateStock($stock, (int)$params['stock_quantity']);
            }

            $stock_status = new StockStatus((int)$params['stock_status']);

            $srbw = $stock->stock_result_by_warehouse;
            $adjustment_quantity = $stock_status->getAdjustmentQuantity($stock->getStockQuantityExceptDisposed());

            $stock = $this->stock_repo->changeStockStatus($stock, $stock_status);
            $srbw->adjustment_quantity = $srbw->adjustment_quantity + $adjustment_quantity;
            $srbw->save();

            return $stock;
        });
    }

    /**
     * 廃棄在庫の取得
     *
     * @param  array $params
     * @return array
     */
    public function searchDisposedStocks(array $params): array
    {
        return $this->stock_repo->searchDisposedStocks($params)
            ->map(function ($s) {
                $s->harvesting_date = $s->getHarvestingDate();
                return $s;
            })
            ->groupBySpecies()
            ->map(function ($species) {
                $species->packaging_styles = $species->stocks->groupByPackagingStyle();

                unset($species->stocks);
                return $species;
            })
            ->all();
    }

    /**
     * 廃棄登録
     *
     * @param  array $stocks
     * @return void
     */
    public function disposeStocks(array $stocks): void
    {
        $this->db->transaction(function () use ($stocks) {
            foreach ($stocks as $stock_id => $params) {
                $stock = $this->stock_repo->disposeStock($stock_id, $params);

                $srbw = $stock->stock_result_by_warehouse;
                $srbw->adjustment_quantity =
                    $srbw->adjustment_quantity + ($stock->disposal_quantity * -1) + $stock->previous_disposal_quantity;
                $srbw->save();
            }
        });
    }

    /**
     * 廃棄伝票の出力
     *
     * @param array $params
     * @param \App\Models\Master\Factory $factory
     */
    public function exportDisposedStocks(array $params, Factory $factory)
    {
        $stocks = $this->stock_repo->searchDisposedStocks($params)->rejectNotDisposed();
        if ($stocks->isEmpty()) {
            throw new InvalidArgumentException('disposed stock does not exist.');
        }

        $file_name = generate_file_name(trans('view.stock.stocks.disposal_stocks_file'));
        $this->excel->create($file_name, function ($excel) use ($factory, $stocks) {
            foreach ($stocks->groupByDisposedDate() as $group) {
                $sheet_name = implode('_', [
                    trans('view.stock.stocks.disposal_stocks_file'),
                    $group->disposal_at->format('Ymd')
                ]);

                $excel->sheet($sheet_name, function ($sheet) use ($factory, $group) {
                    $sheet->setFontFamily('MS PGothic');
                    $sheet->getSheetView()->setZoomScale(90);
                    $sheet->setAutoSize(false);
                    $sheet->getDefaultColumnDimension()->setWidth(3);
                    $sheet->getDefaultRowDimension()->setRowHeight(14);
                    $sheet->cells('A1:AZ45', function ($cells) {
                        $cells->setBackground('FFFFFF');
                    });

                    $sheet->loadView('stock.stocks.export_disposed_stocks', compact('factory', 'group'));
                    $sheet->mergeCells('AO4:AR5');
                    $sheet->mergeCells('AS4:AZ5');
                    $sheet->mergeCells('S6:AH9');
                    $sheet->mergeCells('C21:H21');
                    $sheet->mergeCells('I21:T21');
                    $sheet->mergeCells('U21:W21');
                    $sheet->mergeCells('X21:AA21');
                    $sheet->mergeCells('AB21:AD21');
                    $sheet->mergeCells('AE21:AG21');
                    $sheet->mergeCells('AH21:AX21');

                    $base_row = 22;
                    foreach ($group->stocks as $s) {
                        $sheet->mergeCells('C'.$base_row.':H'.($base_row + 1));
                        $sheet->mergeCells('I'.$base_row.':T'.($base_row + 1));
                        $sheet->mergeCells('U'.$base_row.':W'.($base_row + 1));
                        $sheet->mergeCells('X'.$base_row.':AA'.($base_row + 1));
                        $sheet->mergeCells('AB'.$base_row.':AD'.($base_row + 1));
                        $sheet->mergeCells('AE'.$base_row.':AG'.($base_row + 1));
                        $sheet->mergeCells('AH'.$base_row.':AX'.($base_row + 1));

                        $base_row = $base_row + 2;
                    }
                });
            }
        })
            ->export();
    }

    /**
     * 移動中の在庫データを取得
     *
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function searchMovingStocks(): StockCollection
    {
        return $this->stock_repo->searchMovingStocks();
    }

    /**
     * 収穫日ごとの製品化数量、引当数量を示すリストを取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  array $packaging_style
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return \stdClass
     */
    public function getStocksPerHarvestingDate(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        array $packaging_style,
        Warehouse $warehouse
    ): stdClass {
        $base_list = $harvesting_date->toListOfHarvestingDatesToAllocateProducts();
        $stock_result_by_warehouse_list = $this->stock_result_by_warehouse_repo
            ->getProductStocksPerHarvestingDate($factory, $species, [
                head(head($base_list))->format('Y-m-d'),
                last(last($base_list))->format('Y-m-d')
            ], $packaging_style, $warehouse);

        $harvesting_dates = [];
        foreach ($base_list as $week_idx => $dates) {
            foreach ($dates as $idx => $hd) {
                $srbw = $stock_result_by_warehouse_list->filterByHarvestingDate($hd);
                $harvesting_dates[] = (object)[
                    'date' => $hd->value(),
                    'date_ja' => $hd->formatToJa(),
                    'label_color' => $harvesting_date->getLabelColors()[$week_idx][$idx],
                    'prev_week' => $week_idx === 0,
                    'product_quantity' => $srbw->product_stock_quantity ?? 0,
                    'allocation_quantity' => (int)($srbw->allocation_quantity ?? 0),
                    'stock_quantity' => (int)($srbw->stock_quantity ?? 0)
                ];
            }
        }

        return (object)[
            'harvesting_dates' => $harvesting_dates,
            'total_stock_quantity' => array_reduce($harvesting_dates, function ($total_stock_quantity, $hd) {
                return $total_stock_quantity += $hd->stock_quantity;
            }, 0)
        ];
    }
}
