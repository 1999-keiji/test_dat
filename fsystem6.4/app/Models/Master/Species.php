<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Product;
use App\Models\Master\Collections\SpeciesCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;

class Species extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable, Sortable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'species_code';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    public $sortbale = ['species_code', 'species_name'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 削除可能な商品であるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->products->isEmpty() && $this->factory_species->isEmpty();
    }

    /**
     * 品種に紐づく品種変換マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function species_converters(): HasMany
    {
        return $this->hasMany(SpeciesConverter::class, 'species_code');
    }

    /**
     * 品種に紐づく商品マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'species_code');
    }

    /**
     * 品種に紐づく工場取扱品種マスタを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function factory_species(): HasMany
    {
        return $this->hasMany(FactorySpecies::class, 'species_code');
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\SpeciesCollection
     */
    public function newCollection(array $models = []): SpeciesCollection
    {
        return new SpeciesCollection($models);
    }
}
