<?php

declare(strict_types=1);

namespace App\Models\Stock;

use App\Models\Model;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class StockHistory extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

   /**
    * The primary key for the model.
    *
    * @var array
    */
    protected $primaryKey = [
        'factory_code',
        'sequence_number'
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
    protected $guarded = [
        'created_by',
        'created_at'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stock_history) {
            if (! $stock_history->sequence_number) {
                $stock_history->sequence_number = (
                    $stock_history->where('factory_code', $stock_history->factory_code)
                        ->max('sequence_number') ?: 0
                    ) + 1;
            }
        });
    }
}
