<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use App\Models\Plan\ArrangementDetailState;
use App\Models\Plan\BedState;

class ArrangementDetailStateRepository
{
    /**
     * @var \App\Models\Plan\ArrangementDetailState
     */
    private $model;

    /**
     * @param  \App\Models\Plan\ArrangementDetailState $model
     * @return void
     */
    public function __construct(ArrangementDetailState $model)
    {
        $this->model = $model;
    }

    /**
     * 配置詳細状況データの登録
     *
     * @param  array $arrangement_detail_states
     * @return void
     */
    public function createArrangementDetailStates(array $arrangement_detail_states): void
    {
        foreach (array_chunk($arrangement_detail_states, 500) as $chunked) {
            $this->model->insert($chunked);
        }
    }

    /**
     * 配置詳細状況データの削除
     *
     * @param  \App\Models\Plan\BedState $bed_state
     * @return void
     */
    public function deleteArrangementDetailStates(BedState $bed_state): void
    {
        $this->model->where('factory_code', $bed_state->factory_code)
            ->where('factory_species_code', $bed_state->factory_species_code)
            ->where('start_of_week', $bed_state->start_of_week)
            ->delete();
    }
}
