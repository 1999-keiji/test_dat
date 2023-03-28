<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Model;
use App\Models\Master\Collections\DeliveryWarehouseCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\ShipmentLeadTime;
use App\ValueObjects\Integer\DeliveryLeadTime;
use Kyslik\ColumnSortable\Sortable;

class DeliveryWarehouse extends Model
{
    use AuthorObservable, UpdatedDatetimeObservable, Sortable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['delivery_destination_code', 'warehouse_code'];

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
    protected $guarded = [];

    /**
     * @var array
     */
    public $sortbale = ['delivery_destination_code', 'warehouse_code'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\DeliveryWarehouseCollection
     */
    public function newCollection(array $models = []): DeliveryWarehouseCollection
    {
        return new DeliveryWarehouseCollection($models);
    }

    /**
     * 納入倉庫に紐づく納入先マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_destination(): BelongsTo
    {
        return $this->belongsTo(DeliveryDestination::class, 'delivery_destination_code');
    }

    /**
     * 納入倉庫に紐づく倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_code');
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
     * @return \App\ValueObjects\Integer\DeliveryLeadTime|null
     */
    public function getDeliveryLeadTimeAttribute($value)
    {
        if (! is_null($value)) {
            return new DeliveryLeadTime($value);
        }
    }

    /**
     * @return \App\ValueObjects\Enum\ShipmentLeadTime
     */
    public function getShipmentLeadTimeAttribute($value): ShipmentLeadTime
    {
        return new ShipmentLeadTime($value);
    }

    /**
     * @return string
     */
    public function toJsonToEdit(): string
    {
        return json_encode([
            'delivery_destination_code' => $this->delivery_destination_code,
            'delivery_destination_abbreviation' => $this->delivery_destination->delivery_destination_abbreviation,
            'warehouse_code' => $this->warehouse_code,
            'warehouse_abbreviation' => $this->warehouse->warehouse_abbreviation,
            'delivery_lead_time' => $this->attributes['delivery_lead_time'],
            'shipment_lead_time' => $this->attributes['shipment_lead_time']
        ]);
    }
}
