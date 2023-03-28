<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Excel;
use App\Exceptions\DisposedStockException;
use App\Exceptions\MovedStockException;
use App\Exceptions\MultipleWarehouseStockException;
use App\Models\Master\Factory;
use App\Models\Master\Species;
use App\Models\Shipment\ProductizedResult;
use App\Models\Shipment\Collections\ProductizedResultCollection;
use App\Models\Shipment\Collections\ProductizedResultDetailCollection;
use App\Repositories\Shipment\ProductizedResultRepository;
use App\Repositories\Shipment\ProductizedResultDetailRepository;
use App\Repositories\Stock\StockRepository;
use App\Repositories\Stock\StockResultByWarehouseRepository;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Enum\ShipmentDataExportFile;
use App\ValueObjects\Enum\StockStatus;

class ProductizedResultService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Shipment\ProductizedResultRepository
     */
    private $productized_result_repo;

    /**
     * @var \App\Repositories\Shipment\ProductizedResultDetailRepository
     */
    private $productized_result_detail_repo;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @var \App\Repositories\Stock\StockResultByWarehouseRepository
     */
    private $stock_result_by_warehouse_repo;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param \Maatwebsite\Excel\Excel $excel
     * @param \App\Repositories\Shipment\ProductizedResultRepository $productized_result_repo
     * @param \App\Repositories\Shipment\ProductizedResultDetailRepository $productized_result_detail_repo
     * @param \App\Repositories\Stock\StockRepository $stock_repo
     * @param \App\Repositories\Stock\StockResultByWarehouseRepository $stock_result_by_warehouse_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        ProductizedResultRepository $productized_result_repo,
        ProductizedResultDetailRepository $productized_result_detail_repo,
        StockRepository $stock_repo,
        StockResultByWarehouseRepository $stock_result_by_warehouse_repo
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->productized_result_repo = $productized_result_repo;
        $this->productized_result_detail_repo = $productized_result_detail_repo;
        $this->stock_repo = $stock_repo;
        $this->stock_result_by_warehouse_repo = $stock_result_by_warehouse_repo;
    }

    /**
     * 製品化実績データを条件に応じて検索
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return \App\Models\Shipment\Collections\ProductizedResultCollection
     */
    public function searchProductizedResults(Factory $factory, array $params): ProductizedResultCollection
    {
        return $this->productized_result_repo->search($params)->rejectNotInputtable($factory);
    }

    /**
     * 製品化実績データ取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Shipment\ProductizedResult
     */
    public function getProductizedResult(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): ?ProductizedResult {
        return $this->productized_result_repo->getProductizedResult($factory, $species, $harvesting_date);
    }

    /**
     * 製品化実績明細データ取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Shipment\Collections\ProductizedResultDetailCollection
     */
    public function getProductizedResultDetails(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date
    ): ProductizedResultDetailCollection {
        return $this->productized_result_detail_repo->getProductizedResultDetails($factory, $species, $harvesting_date);
    }

    /**
     * 日別製品化率・出荷重量一覧データの出力
     *
     * @param array $params
     * @param Factory $factory
     */
    public function exportProductizedResultsPerHarvestingDate(array $params, Factory $factory)
    {
        $harvesting_date_from = HarvestingDate::parse($params['harvesting_date']['from']);
        $harvesting_date_to = HarvestingDate::parse($params['harvesting_date']['to']);

        $harvesting_dates = [];
        while ($harvesting_date_from->lte($harvesting_date_to)) {
            $harvesting_dates[] = $harvesting_date_from;
            $harvesting_date_from = $harvesting_date_from->addDay();
        }

        $productized_results = $this->productized_result_repo
            ->getProductizedResultsByHarvestingDate($factory, $harvesting_dates);

        $shipment_data_export_file = new ShipmentDataExportFile((int)$params['shipment_data_export_file']);
        $file_name = generate_file_name($shipment_data_export_file->label(), [
            $factory->factory_abbreviation,
            head($harvesting_dates)->format('Ymd'),
            last($harvesting_dates)->format('Ymd')
        ]);

        $this->excel
            ->create($file_name, function ($excel) use ($factory, $harvesting_dates, $productized_results) {
                $sheet_name = $factory->factory_abbreviation;
                $excel->sheet($sheet_name, function ($sheet) use ($factory, $harvesting_dates, $productized_results) {
                    $average_weights = $factory->factory_species->getAverageWeightsPerSpecies();
                    $sheet->loadView('shipment.shipment_data_export.export_by_day')
                        ->with('factory', $factory)
                        ->with('harvesting_dates', $harvesting_dates)
                        ->with('productized_results', $productized_results)
                        ->with('average_weights', $average_weights);
                });
            })
            ->export();
    }

    /**
     * 製品化実績の保存
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  array $productized_result_param
     * @param  array $productized_result_details_param
     * @return void
     */
    public function saveProductizedResult(
        Factory $factory,
        Species $species,
        HarvestingDate $harvesting_date,
        array $productized_result_params,
        array $productized_result_details
    ): void {
        $warehouse = $factory->getDefaultWarehouse();
        $params = [
            'factory_code' => $factory->factory_code,
            'species_code' => $species->species_code,
            'harvesting_date' => $harvesting_date->toDateString()
        ];

        $this->db->transaction(function () use (
            $productized_result_params,
            $productized_result_details,
            $warehouse,
            $params
        ) {
            try {
                $productized_result = $this->productized_result_repo->find($params);

                $this->productized_result_repo->update($productized_result, $productized_result_params);
                foreach ($productized_result_details as $prd) {
                    $existed = $productized_result->productized_result_details->filterByPackagingStyle($prd)->first();
                    if (is_null($existed)) {
                        $prd = $this->productized_result_detail_repo->create(array_merge($params, $prd));
                        $srbw = $this->stock_result_by_warehouse_repo->createProductedStock($prd, $warehouse);
                        $this->stock_repo->createProductedStock($srbw);
                    }

                    if (! is_null($existed)) {
                        $current_product_quantity = $existed->product_quantity;
                        $diff = $current_product_quantity - (int)$prd['product_quantity'];

                        $prd = $this->productized_result_detail_repo->update($existed, (int)$prd['product_quantity']);
                        $srbw = $this->stock_result_by_warehouse_repo->updateProductedStock($prd);

                        $stocks = $this->stock_repo->findNotAllocatedStocks(array_merge(
                            $prd->toArray(),
                            ['stock_status' => StockStatus::NORMAL]
                        ));

                        if ($stocks->groupby('warehouse_code')->count() > 1) {
                            throw new MultipleWarehouseStockException(
                                'multiple warehouse stock exists. :'.$stocks->toJson()
                            );
                        }
                        if ($stocks->sumOfDisposedQuantity() !== 0) {
                            throw new DisposedStockException(
                                'disposed already. :'.$stocks->toJson()
                            );
                        }
                        if ($warehouse->warehouse_code !== $stocks->first()->warehouse_code) {
                            throw new MovedStockException(
                                'moved already. :'.$srbw->toJson()
                            );
                        }

                        if ($diff > 0) {
                            $stocks->subtractStockQuantityWithAllocation($diff)
                                ->each(function ($s) {
                                    $this->stock_repo->subtractProductedStock($s);
                                });
                        }

                        if ($diff < 0) {
                            $allocation_quantity = $current_product_quantity - $stocks->sumOfStockQuantity();
                            $stocks->each(function ($s) {
                                $this->stock_repo->resetProductedStock($s);
                            });

                            $srbw->product_stock_quantity -= $allocation_quantity;
                            $this->stock_repo->createProductedStock($srbw);
                        }
                    }
                }
            } catch (ModelNotFoundException $e) {
                $this->productized_result_repo->create(array_merge($params, $productized_result_params));
                foreach ($productized_result_details as $prd) {
                    $prd = $this->productized_result_detail_repo->create(array_merge($params, $prd));
                    $srbw = $this->stock_result_by_warehouse_repo->createProductedStock($prd, $warehouse);
                    $this->stock_repo->createProductedStock($srbw);
                }
            }
        });
    }
}
