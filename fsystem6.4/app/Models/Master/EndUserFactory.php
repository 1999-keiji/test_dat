<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\Models\Master\Collections\EndUserFactoryCollection;

class EndUserFactory extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['end_user_code', 'factory_code'];

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
    protected $fillable = ['end_user_code', 'factory_code'];

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', array_only($this->attributes, $this->getKeyName()));
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\EndUserFactoryCollection
     */
    public function newCollection(array $models = []): EndUserFactoryCollection
    {
        return new EndUserFactoryCollection($models);
    }

    /**
     * エンドユーザに紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code');
    }
}
