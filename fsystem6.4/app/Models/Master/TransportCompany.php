<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\TransportCompanyCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class TransportCompany extends Model
{
    use Sortable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'transport_company_code';

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
     * @var array
     */
    public $sortbale = ['transport_company_code', 'company_name'];

    /**
     * 削除可能なマスタかどうか判定する
     *
     * @return bool
     */
    public function isDeletable()
    {
        return $this->collection_times->isEmpty();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\TransportCompanyCollection
     */
    public function newCollection(array $models = []): TransportCompanyCollection
    {
        return new TransportCompanyCollection($models);
    }

    /**
     * 運送会社に紐づく集荷時間マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collection_times(): HasMany
    {
        return $this->hasMany(CollectionTime::class, 'transport_company_code');
    }
}
