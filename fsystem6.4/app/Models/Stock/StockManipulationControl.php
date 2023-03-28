<?php

namespace App\Models\Stock;

use App\Models\Model;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class StockManipulationControl extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stock_manipulation_control';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'factory_code';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['factory_code'];
}
