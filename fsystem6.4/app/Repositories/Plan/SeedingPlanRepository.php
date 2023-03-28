<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use App\Models\Plan\BedState;
use App\Models\Plan\SeedingPlan;

class SeedingPlanRepository
{
    /**
     * @var \App\Models\Plan\SeedingPlan
     */
    private $model;

    /**
     * @param  \App\Models\Plan\SeedingPlan $model
     * @return void
     */
    public function __construct(SeedingPlan $model)
    {
        $this->model = $model;
    }

    /**
     * 播種計画データの登録
     *
     * @param  array $seeding_plans
     * @return void
     */
    public function createSeedingPlans(array $seeding_plans): void
    {
        $this->model->insert($seeding_plans);
    }

    /**
     * 播種計画データの削除
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @return void
     */
    public function deleteSeedingPlans(BedState $bed_state): void
    {
        $this->model->where('factory_code', $bed_state->factory_code)
            ->where('factory_species_code', $bed_state->factory_species_code)
            ->where('start_of_week', $bed_state->start_of_week)
            ->delete();
    }
}
