<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\PanelStateCollection;
use App\ValueObjects\Enum\GrowingStage;

class PanelState extends Model
{
    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'panel_state';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'panel_id', 'date'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function newCollection(array $models = []): PanelStateCollection
    {
        return new PanelStateCollection($models);
    }

    /**
     * @return App\ValueObjects\Enum\GrowingStage
     */
    public function getGrowingStage(): GrowingStage
    {
        return new GrowingStage($this->growing_stage);
    }
}
