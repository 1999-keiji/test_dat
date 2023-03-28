<?php

declare(strict_types=1);

namespace App\Models\Plan;

use App\Models\Model;
use App\Models\Plan\Collections\CropCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Crop extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'crop';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'factory_code',
        'species_code',
        'number_of_heads',
        'weight_per_number_of_heads',
        'input_group',
        'date'
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
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Plan\Collections\CropCollection
     */
    public function newCollection(array $models = []): CropCollection
    {
        return new CropCollection($models);
    }

    /**
     * @return string
     */
    public function getPackagingStyleAttribute(): string
    {
        $weight_per_number_of_heads = $this->weight_per_number_of_heads.'g';
        if ($this->weight_per_number_of_heads >= 1000) {
            $weight_per_number_of_heads = round($this->weight_per_number_of_heads / 1000).'kg';
        }

        return implode(' ', [
            $weight_per_number_of_heads,
            config('constant.master.factory_products.input_group')[$this->input_group]
        ]);
    }
}
