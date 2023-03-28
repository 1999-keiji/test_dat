<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Collections\CollectionTimeCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class CollectionTime extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable;

    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'collection_time';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['transport_company_code', 'sequence_number'];

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
     * 削除可能なマスタかどうか判定する
     *
     * @return bool
     */
    public function isDeletable()
    {
        return $this->delivery_destinations->isEmpty();
    }

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
     * @return \App\Models\Master\Collections\CollectionTimeCollection
     */
    public function newCollection(array $models = []): CollectionTimeCollection
    {
        return new CollectionTimeCollection($models);
    }

    /**
     * 集荷時間に紐づく納入先マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_destinations(): HasMany
    {
        return $this->hasMany(DeliveryDestination::class, 'transport_company_code', 'transport_company_code')
            ->where('collection_time_sequence_number', $this->sequence_number);
    }
}
