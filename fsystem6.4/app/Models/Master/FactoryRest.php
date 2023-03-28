<?php

declare(strict_types=1);

namespace App\Models\Master;

use App\Models\Model;
use App\Models\Master\Collections\FactoryRestCollection;
use App\Traits\AuthorObservable;

class FactoryRest extends Model
{
    use AuthorObservable;

    /**
     *
     * @var string
     */
    protected $table = 'factory_rest';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['factory_code', 'date'];

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
     * @return \App\Models\Master\Collections\FactoryRestCollection
     */
    public function newCollection(array $models = []): FactoryRestCollection
    {
        return new FactoryRestCollection($models);
    }

    /**
     * 休み状態を返却
     *
     * @return array
     */
    public function getRestList(): array
    {
        $rest_list = [];
        if ($this->factory_is_rest) {
            $rest_list[] = trans('view.master.factory_rest.factory_is_rest');
        }
        if ($this->shipment_is_rest) {
            $rest_list[] = trans('view.master.factory_rest.shipment_is_rest');
        }
        if ($this->delivery_is_rest) {
            $rest_list[] = trans('view.master.factory_rest.delivery_is_rest');
        }

        return $rest_list;
    }
}
