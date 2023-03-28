<?php

declare(strict_types=1);

namespace App\Models\Stock\Collections;

use stdClass;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use App\Exceptions\OverAllocationException;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\WorkingDate;

class StockCollection extends Collection
{
    /**
     * 工場を条件に抽出
     *
     * @param  \stdClass $factory
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function filterByFactory(stdClass $factory): StockCollection
    {
        return $this->where('factory_code', $factory->factory_code);
    }

    /**
     * 品種単位でグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupBySpecies(): BaseCollection
    {
        return $this->groupBy('species_code')
            ->map(function ($grouped, $species_code) {
                return (object)[
                    'species_code' => $species_code,
                    'species_name' => $grouped->first()->species_name ?: $grouped->first()->species_abbreviation,
                    'stocks' => $grouped,
                    'count' => $grouped->count()
                ];
            })
            ->values();
    }

    /**
     * 在庫数の合計を取得
     *
     * @return int
     */
    public function sumOfStockQuantity(): int
    {
        return $this->sum('stock_quantity');
    }

    /**
     * 廃棄数を除いた在庫数の合計を取得
     *
     * @return int
     */
    public function getSumOfStockQuantityExceptDisposed(): int
    {
        return $this
            ->map(function ($s) {
                return $s->getStockQuantityExceptDisposed();
            })
            ->sum();
    }

    /**
     * 在庫株数の合計を取得
     *
     * @return int
     */
    public function sumOfStockNumber(): int
    {
        return (int)$this->sum('stock_number');
    }

    /**
     * 在庫重量の合計を取得
     *
     * @return int
     */
    public function sumOfStockWeight(): int
    {
        return $this->sum('stock_weight');
    }

    /**
     * 廃棄数の合計を取得
     *
     * @return int
     */
    public function sumOfDisposedQuantity(): int
    {
        return $this->sum('disposal_quantity');
    }

    /**
     * 商品規格単位でグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByPackagingStyle(): BaseCollection
    {
        return $this
            ->groupBy(function ($s) {
                return implode('|', [
                    $s->number_of_heads,
                    $s->weight_per_number_of_heads,
                    $s->input_group
                ]);
            })
            ->map(function ($grouped, $packaging_style) {
                return (object)(
                    array_combine(
                        ['number_of_heads', 'weight_per_number_of_heads', 'input_group'],
                        explode('|', $packaging_style)
                    ) +
                    ['stocks' => $grouped]
                );
            })
            ->values();
    }

    /**
     * 収穫日ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByHarvestingDate(): BaseCollection
    {
        return $this->groupBy('harvesting_date')
            ->map(function ($grouped, $harvesting_date) {
                return (object)[
                    'harvesting_date' => HarvestingDate::parse($harvesting_date),
                    'stocks' => $grouped
                ];
            })
            ->values();
    }

    /**
     * 廃棄数量がゼロのものを排除
     *
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function rejectNotDisposed(): StockCollection
    {
        return $this->reject(function ($s) {
            return $s->disposal_quantity === 0;
        });
    }

    /**
     * 廃棄日ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByDisposedDate(): BaseCollection
    {
        return $this->groupBy('disposal_at')
            ->map(function ($grouped, $disposal_at) {
                return (object)[
                    'disposal_at' => WorkingDate::parse($disposal_at),
                    'stocks' => $grouped
                ];
            })
            ->values();
    }

    /**
     * 引当量に応じて在庫数を減算する
     *
     * @param  int $allocation_quantity
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function subtractStockQuantityWithAllocation(int $allocation_quantity): StockCollection
    {
        foreach ($this as $s) {
            $current_stock_quantity = $s->stock_quantity - $s->disposal_quantity;
            if ($current_stock_quantity === 0) {
                continue;
            }

            $quantity = ($current_stock_quantity >= $allocation_quantity) ?
                $allocation_quantity :
                $current_stock_quantity;

            $s->stock_quantity = $current_stock_quantity - $quantity + $s->disposal_quantity;
            $s->stock_weight = $s->stock_quantity * $s->weight_per_number_of_heads;

            $allocation_quantity -= $quantity;
            if ($allocation_quantity === 0) {
                break;
            }
        }

        if ($allocation_quantity > 0) {
            $message = 'stock was lacked. allocation quantity: %d. stocks: %s';
            throw new OverAllocationException(sprintf($message, $allocation_quantity, $this->toJson()));
        }

        return $this;
    }

    /**
     * 収穫日の昇順に並び替え
     *
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function sortByHarvestingDate(): StockCollection
    {
        return $this->sortBy('harvesting_date');
    }

    /**
     * 未引当在庫を抽出
     *
     * @return \App\Models\Stock\Collections\StockCollection
     */
    public function filterNotAllocated()
    {
        return $this->filter(function ($s) {
            return $s->stock_quantity > 0 && $s->stock_quantity !== $s->disposal_quantity && is_null($s->order_number);
        });
    }
}
