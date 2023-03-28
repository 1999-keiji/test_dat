<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use App\Models\Plan\BedState;
use App\Models\Plan\CultivationState;

class CultivationStateRepository
{
    /**
     * @var \App\Models\Plan\CultivationState
     */
    private $model;

    /**
     * @param  \App\Models\Plan\CultivationState $model
     * @return void
     */
    public function __construct(CultivationState $model)
    {
        $this->model = $model;
    }

    /**
     * 栽培状況データの登録
     *
     * @param  array $cultivation_states
     * @return void
     */
    public function createCultivationStates(array $cultivation_states): void
    {
        $this->model->insert($cultivation_states);
    }

    /**
     * 栽培状況データの削除
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @return void
     */
    public function deleteCultivationStates(BedState $bed_state): void
    {
        $this->model->where('factory_code', $bed_state->factory_code)
            ->where('factory_species_code', $bed_state->factory_species_code)
            ->where('start_of_week', $bed_state->start_of_week)
            ->delete();
    }
}
