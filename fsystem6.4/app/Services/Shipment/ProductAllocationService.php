<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use Illuminate\Database\Connection;
use Maatwebsite\Excel\Excel;
use App\Exceptions\OverAllocationException;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Models\Master\Warehouse;
use App\Models\Order\Collections\OrderCollection;
use App\Models\Stock\Collections\StockCollection;
use App\Repositories\Master\EndUserRepository;
use App\Repositories\Shipment\ProductAllocationRepository;
use App\Repositories\Stock\StockRepository;
use App\ValueObjects\Enum\ShipmentDataExportFile;
use App\ValueObjects\Date\DeliveryDate;

class ProductAllocationService
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
     * @var \App\Repositories\Shipment\ProductAllocationRepository
     */
    private $product_allocation_repo;

    /**
     * @var \App\Repositories\Stock\StockRepository
     */
    private $stock_repo;

    /**
     * @var \App\Repositories\Master\EndUserRepository
     */
    private $end_user_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Shipment\ProductAllocationRepository $product_allocation_repository
     * @param  \App\Repositories\Stock\StockRepository $stock_repository
     * @param  \App\Repositories\Master\EndUserRepository $end_user_repository
     * @return void
     */
    public function __construct(
        Connection $db,
        Excel $excel,
        ProductAllocationRepository $product_allocation_repository,
        StockRepository $stock_repository,
        EndUserRepository $end_user_repository
    ) {
        $this->db = $db;
        $this->excel = $excel;
        $this->product_allocation_repo = $product_allocation_repository;
        $this->stock_repo = $stock_repository;
        $this->end_user_repo = $end_user_repository;
    }

    /**
     * 顧客別出荷実績の出力
     *
     * @param array $params
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\Customer $customer
     */
    public function exportOrdersAndProductAllocationsPerCustomer(array $params, Factory $factory, ?Customer $customer)
    {
        $product_allocations = $this->product_allocation_repo
            ->getOrdersAndProductAllocationsPerCustomer($params);

        $end_users = $this->end_user_repo
            ->getCurrentApplicatedEndUsers($product_allocations->pluck('end_user_code')->all());

        $first_harvesting_dates = $product_allocations
            ->groupBy('order_number')
            ->map(function ($grouped, $order_number) {
                return [
                    'order_number' => $order_number,
                    'harvesting_date' => $grouped->sortBy('harvesting_date')->first()->harvesting_date
                ];
            })
            ->pluck('harvesting_date', 'order_number')
            ->all();

        foreach ($product_allocations as $pa) {
            $pa->end_user_abbreviation = $end_users->findByEndUserCode($pa->end_user_code)->end_user_abbreviation ?? '';
            $pa->order_amount = $first_harvesting_dates[$pa->order_number]->eq($pa->harvesting_date)
                ? $pa->order_amount
                : 0;
        }

        $shipment_data_export_file = new ShipmentDataExportFile((int)$params['shipment_data_export_file']);
        $delivery_date = [
            'from' => new DeliveryDate($params['delivery_date']['from']),
            'to' => new DeliveryDate($params['delivery_date']['to'])
        ];

        $file_name = generate_file_name($shipment_data_export_file->label(), [
            $factory->factory_abbreviation,
            $delivery_date['from']->format('Ymd'),
            $delivery_date['to']->format('Ymd'),
        ]);

        $this->excel->create($file_name, function ($excel) use (
            $params,
            $factory,
            $customer,
            $product_allocations,
            $delivery_date
        ) {
            $excel->sheet($factory->factory_abbreviation, function ($sheet) use (
                $params,
                $factory,
                $customer,
                $product_allocations,
                $delivery_date
            ) {
                $sheet->loadView('shipment.shipment_data_export.export_by_customer')
                    ->with('params', $params)
                    ->with('factory', $factory)
                    ->with('customer', $customer)
                    ->with('product_allocations', $product_allocations);
            });
        })
            ->export();
    }

    /**
     * 製品引当数の保存
     *
     * @param  array $factory_products
     * @param  \App\Models\Master\Warehouse $warehouse
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @return void
     * @throws \App\Exceptions\OverAllocationException
     */
    public function saveProductAllocations(array $factory_products, Warehouse $warehouse, OrderCollection $orders): void
    {
        $this->db->transaction(function () use ($factory_products, $warehouse, $orders) {
            $product_allocations = $stocks = [];
            foreach ($orders as $o) {
                $o->product_allocations->each(function ($pa) {
                    $pa->delete();
                });

                $o->stocks->each(function ($s) {
                    $this->stock_repo->resetAllocation($s);
                });

                $harvesting_dates = $factory_products[$o->factory_product_sequence_number]
                    [$o->delivery_destination_code]
                    [$o->shipping_date] ?? [];

                if (count($harvesting_dates) === 0) {
                    continue;
                }

                $product_quantity = $o->order_quantity * $o->number_of_cases; // 注文数あたり必要な製品数
                foreach ($harvesting_dates as $hd => $quantity) {
                    $allocation_quantity = (int)($product_quantity >= $quantity ? $quantity : $product_quantity);
                    if ($allocation_quantity === 0) {
                        continue;
                    }

                    $factory_products[$o->factory_product_sequence_number]
                        [$o->delivery_destination_code]
                        [$o->shipping_date]
                        [$hd] -= $allocation_quantity;
                    $product_quantity -= $allocation_quantity;

                    $product_allocations[] = [
                        'factory_code' => $o->factory_code,
                        'species_code' => $o->species_code,
                        'harvesting_date' => $hd,
                        'warehouse_code' => $warehouse->warehouse_code,
                        'order_number' => $o->order_number,
                        'allocation_quantity' => $allocation_quantity
                    ];

                    $stocks[] = [
                        'factory_code' => $o->factory_code,
                        'warehouse_code' => $warehouse->warehouse_code,
                        'species_code' => $o->species_code,
                        'harvesting_date' => $hd,
                        'number_of_heads' => $o->number_of_heads,
                        'weight_per_number_of_heads' => $o->weight_per_number_of_heads,
                        'input_group' => $o->input_group,
                        'order' => $o,
                        'stock_quantity' => $allocation_quantity,
                        'stock_weight' => $allocation_quantity * $o->weight_per_number_of_heads
                    ];

                    if ($product_quantity === 0) {
                        break;
                    }
                }
            }

            $this->product_allocation_repo->insertProductAllocations($product_allocations);

            StockCollection::make($stocks)
                ->groupByHarvestingDate()
                ->map(function ($group) {
                    $params = array_only($group->stocks->first(), [
                        'factory_code',
                        'warehouse_code',
                        'species_code',
                        'harvesting_date',
                        'number_of_heads',
                        'weight_per_number_of_heads',
                        'input_group'
                    ]);

                    $stocks = $this->stock_repo->findNotAllocatedStocks($params);
                    if ($stocks->isEmpty()) {
                        throw new OverAllocationException('stock does not exist.'.json_encode($params));
                    }

                    $stocks->subtractStockQuantityWithAllocation($group->stocks->sumOfStockQuantity())
                        ->each(function ($s) {
                            $this->stock_repo->subtractStockQuantity($s);
                        });
                });

            $this->stock_repo->insertAllocationStocks($stocks);
        });
    }
}
