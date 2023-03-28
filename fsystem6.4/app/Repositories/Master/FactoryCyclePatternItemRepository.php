<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Database\Connection;
use App\Models\Master\FactoryCyclePattern;
use App\Models\Master\FactoryCyclePatternItem;
use App\Models\Master\Collections\FactoryCyclePatternItemCollection;

class FactoryCyclePatternItemRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Master\FactoryCyclePatternItem
     */
    private $model;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param \App\Models\Master\FactoryCyclePatternItem $model
     * @return void
     */
    public function __construct(Connection $db, FactoryCyclePatternItem $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 工場サイクルパターン詳細の登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryCyclePatternItem
     */
    public function create(array $params): FactoryCyclePatternItem
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * サイクルパターン詳細削除
     *
     * @param \App\Models\Master\FactoryCyclePattern $factory_cycle_pattern
     * @return void
     */
    public function deleteFactoryCyclePatternItems(FactoryCyclePattern $factory_cycle_pattern): void
    {
        $this->db->table($this->model->getTable())
            ->where('factory_code', $factory_cycle_pattern->factory_code)
            ->where('cycle_pattern_sequence_number', $factory_cycle_pattern->sequence_number)
            ->delete();
    }
}
