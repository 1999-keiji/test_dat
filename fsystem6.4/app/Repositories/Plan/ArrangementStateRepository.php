<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use App\Models\Plan\ArrangementState;
use App\Models\Plan\BedState;

class ArrangementStateRepository
{
    /**
     * @var \App\Models\Plan\ArrangementState
     */
    private $model;

    /**
     * @param  \App\Models\Plan\ArrangementState $model
     * @return void
     */
    public function __construct(ArrangementState $model)
    {
        $this->model = $model;
    }

    /**
     * 配置状況データの登録
     *
     * @param  array $arrangement_states
     * @return void
     */
    public function createArrangementStates(array $arrangement_states): void
    {
        foreach (array_chunk($arrangement_states, 500) as $chunked) {
            $this->model->insert($chunked);
        }
    }

    /**
     * 配置状況データの削除
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @return void
     */
    public function deleteArrangementStates(BedState $bed_state): void
    {
        $this->model->where('factory_code', $bed_state->factory_code)
            ->where('factory_species_code', $bed_state->factory_species_code)
            ->where('start_of_week', $bed_state->start_of_week)
            ->delete();
    }
}
