<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\GrowthSimulationItem;

class GrowthSimulationItemRepository
{
    /**
     * @var \App\Models\Plan\GrowthSimulationItem
     */
    private $model;

    /**
     * @param  \App\Models\Plan\GrowthSimulationItem $model
     * @return void
     */
    public function __construct(GrowthSimulationItem $model)
    {
        $this->model = $model;
    }

    /**
     * 生産シミュレーション明細の登録
     *
     * @param  array $growth_simulation_items
     * @return void
     */
    public function insertGrowthSimulationItems(array $growth_simulation_items): void
    {
        $this->model->insert($growth_simulation_items);
    }

    /**
     * 生産シミュレーション明細の削除
     *
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return void
     */
    public function deleteGrowthSimulationItems(GrowthSimulation $growth_simulation): void
    {
        $this->model
            ->where('factory_code', $growth_simulation->factory_code)
            ->where('simulation_id', $growth_simulation->simulation_id)
            ->where('factory_species_code', $growth_simulation->factory_species_code)
            ->delete();
    }
}
