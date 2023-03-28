<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\DisplayKubun;

class PlannedArrangementStatusWork extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * @var int
     */
    public const NUMBER_OF_ROWS_COLUMN = 30;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'planned_arrangement_status_work';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'simulation_id',
        'factory_species_code',
        'display_kubun',
        'date',
        'bed_column'
    ];

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
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection
     */
    public function newCollection(array $models = []): PlannedArrangementStatusWorkCollection
    {
        return new PlannedArrangementStatusWorkCollection($models);
    }

    /**
     * 「生育ステージ段1～30」に値が登録されている数を取得
     *
     * @param  int $growing_stage_sequence_number
     * @return int $count
     */
    public function getGrowingStagesCountRegisteredCount(int $growing_stage_sequence_number): int
    {
        $range = range(1, self::NUMBER_OF_ROWS_COLUMN);
        return array_reduce($range, function ($count, $row) use ($growing_stage_sequence_number) {
            if ($this['growing_stages_count_'.$row] === $growing_stage_sequence_number) {
                $count += 1;
            }
            if ($this['growing_stages_count_'.$row] === 0 &&
                $this['fixed_growing_stages_count_'.$row] === $growing_stage_sequence_number) {
                $count -= 1;
            }

            return $count;
        }, 0);
    }
}
