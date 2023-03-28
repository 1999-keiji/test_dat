<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\FactoryCyclePattern;
use App\Models\Master\Collections\FactoryCyclePatternCollection;
use App\Models\Plan\Collections\GrowthSimulationCollection;

class FactoryCyclePatternRepository
{
    /**
     * @var \App\Models\Master\FactoryCyclePattern
     */
    private $model;

    /**
     * @param \App\Models\Master\FactoryCyclePattern $model
     * @return void
     */
    public function __construct(FactoryCyclePattern $model)
    {
        $this->model = $model;
    }

    /**
     * 主キーにより、サイクルパターンを取得
     *
     * @param  array $primary_key
     * @return \App\Models\Model\FactoryCyclePattern
     */
    public function find(array $primary_key): FactoryCyclePattern
    {
        $query = $this->model->newQuery();
        foreach ($this->model->getKeyName() as $key) {
            $query->where($key, $primary_key[$key]);
        }

        return $query->first();
    }

    /**
     * 工場サイクルパターンマスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryCyclePattern
     */
    public function create(array $params): FactoryCyclePattern
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 工場サイクルパターンマスタの更新
     *
     * @param  \App\Models\Master\FactoryCyclePattern $factory_cycle_pattern
     * @param  string $cycle_pattern_name
     * @return \App\Models\Master\FactoryCyclePattern
     */
    public function update(
        FactoryCyclePattern $factory_cycle_pattern,
        string $cycle_pattern_name
    ): FactoryCyclePattern {
        $factory_cycle_pattern->cycle_pattern_name = $cycle_pattern_name;
        $factory_cycle_pattern->save();

        return $factory_cycle_pattern;
    }
}
