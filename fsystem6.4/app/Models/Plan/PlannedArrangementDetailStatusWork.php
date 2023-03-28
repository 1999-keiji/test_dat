<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection;
use App\Traits\AuthorObservable;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementDetailStatusWork extends Model
{
    use AuthorObservable;

    /**
     * @var int
     */
    public const NUMBER_OF_PANEL_STATE_COLUMN = 100;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'planned_arrangement_detail_status_work';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'simulation_id', 'factory_species_code', 'display_kubun', 'date', 'bed_row', 'bed_column'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_by', 'created_at'];

    /**
     * @return \App\ValueObjects\Enum\DisplayKubun
     */
    public function getDisplayKubun(): DisplayKubun
    {
        return new DisplayKubun($this->display_kubun);
    }

    /**
     * @return \App\ValueObjects\Enum\DisplayKubun
     */
    public function getPrevDisplayKubun(): DisplayKubun
    {
        return new DisplayKubun($this->prev_display_kubun);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection
     */
    public function newCollection(array $models = []): PlannedArrangementDetailStatusWorkCollection
    {
        return new PlannedArrangementDetailStatusWorkCollection($models);
    }
}
