<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\DeliveryDestinationCollection;
use App\Traits\AuthorObservable;
use App\Traits\DataLinkable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Integer\DeliveryLeadTime;

class DeliveryDestination extends Model
{
    use Sortable, DataLinkable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'delivery_destination_code';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'shipping_label_unnecessary_flag' => 'boolean',
        'export_target_flag' => 'boolean',
        'needs_to_subtract_printing_delivery_date' => 'boolean',
        'can_display' => 'boolean'
    ];

    /**
     * @var array
     */
    public $sortbale = ['delivery_destination_code', 'delivery_destination_name'];

    /**
     * @var array
     */
    private const LINKED_COLUMNS = [
        'delivery_destination_code',
        'delivery_destination_name',
        'delivery_destination_name2',
        'delivery_destination_abbreviation',
        'delivery_destination_name_kana',
        'delivery_destination_name_english',
        'country_code',
        'postal_code',
        'prefecture_code',
        'address',
        'address2',
        'address3',
        'abroad_address',
        'abroad_address2',
        'abroad_address3',
        'phone_number',
        'extension_number',
        'fax_number',
        'mail_address',
        'staff_abbreviation',
        'statement_of_delivery_message',
        'statement_of_delivery_output_class',
        'shipping_label_unnecessary_flag',
        'export_target_flag',
        'shipment_way_class',
        'delivery_destination_class',
        'cii_company_code',
        'itm_class2',
        'itm_class3',
        'itm_class4',
        'itm_class5',
        'itm_flag1',
        'itm_flag2',
        'itm_flag3',
        'itm_flag4',
        'itm_flag5',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\DeliveryDestinationCollection
     */
    public function newCollection(array $models = []): DeliveryDestinationCollection
    {
        return new DeliveryDestinationCollection($models);
    }

    /**
     * 削除可能な納入先であるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->creating_type->isDeletableCreatingType() &&
            $this->delivery_warehouses->isEmpty() &&
            $this->delivery_factory_products->isEmpty();
    }

    /**
     * 工場を条件に配送リードタイムを取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @return \App\ValueObjects\Integer\DeliveryLeadTime
     */
    public function getDeliveyLeadTimeByFactory(Factory $factory): DeliveryLeadTime
    {
        $default_delivery_lead_time = (new DeliveryLeadTime())->getDefaultDeliveryLeadTime();

        $delivery_warehouse = $this->delivery_warehouses->filterByFactory($factory);
        if (is_null($delivery_warehouse)) {
            return $default_delivery_lead_time;
        }
        if (is_null($delivery_warehouse->delivery_lead_time)) {
            return $default_delivery_lead_time;
        }

        return $delivery_warehouse->delivery_lead_time;
    }

    /**
     * 納入先に紐づくエンドユーザマスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function end_user(): BelongsTo
    {
        return $this->belongsTo(EndUser::class, 'end_user_code', 'end_user_code')
            ->whereRaw('application_started_on <= CURRENT_DATE')
            ->orderBy('application_started_on', 'DESC');
    }

    /**
     * 納入先に紐づく倉庫マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_warehouses(): HasMany
    {
        return $this->hasMany(DeliveryWarehouse::class, 'delivery_destination_code');
    }

    /**
     * 納入先に紐づく納入工場商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_factory_products(): HasMany
    {
        return $this->hasMany(DeliveryFactoryProduct::class, 'delivery_destination_code');
    }

    /**
     * @return \App\ValueObjects\Enum\CreatingType
     */
    public function getCreatingTypeAttribute($value): CreatingType
    {
        return new CreatingType($this->attributes['creating_type']);
    }
}
