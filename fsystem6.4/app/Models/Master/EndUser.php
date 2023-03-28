<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\EndUserCollection;
use App\Traits\AuthorObservable;
use App\Traits\DataLinkable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\String\EndUserCode;

class EndUser extends Model
{
    use Sortable, DataLinkable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['end_user_code', 'application_started_on'];

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
        'lot_managing_target_flag' => 'boolean',
        'export_target_flag' => 'boolean',
        'group_company_flag' => 'boolean'
    ];

    /**
     * @var array
     */
    public $sortbale = ['end_user_code', 'application_started_on', 'customer_code', 'end_user_name', 'address', 'phone_number'];

    /**
     * @var array
     */
    private const LINKED_COLUMNS = [
        'end_user_code',
        'application_started_on',
        'customer_code',
        'end_user_name',
        'end_user_name2',
        'end_user_abbreviation',
        'end_user_name_kana',
        'end_user_name_english',
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
        'mail_address',
        'end_user_staff_name',
        'currency_code',
        'delivery_destination_code',
        'seller_code',
        'seller_name',
        'pickup_slip_message',
        'statement_of_delivery_class',
        'statement_of_delivery_price_show_class',
        'abroad_shipment_price_show_class',
        'export_managing_class',
        'export_exchange_rate_code',
        'remarks1',
        'remarks2',
        'remarks3',
        'remarks4',
        'remarks5',
        'remarks6',
        'loading_port_code',
        'loading_port_name',
        'drop_port_code',
        'drop_port_name',
        'exchange_rate_port_code',
        'exchange_rate_port_name',
        'lot_managing_target_flag',
        'end_user_remark',
        'end_user_request_number',
        'statement_of_delivery_remark_class',
        'statement_of_delivery_buyer_remark_class',
        'export_target_flag',
        'group_company_flag',
        'company_code',
        'company_name',
        'company_abbreviation',
        'company_name_kana',
        'company_name_english',
        'company_group_code',
        'company_group_name',
        'company_group_name_english'
    ];

    /**
     * 主キーをパイプつなぎにして返却
     *
     * @return string
     */
    public function getJoinedPrimaryKeys(): string
    {
        return implode('|', [
            $this->end_user_code,
            $this->application_started_on->format('Y-m-d')
        ]);
    }

    /**
     * 削除可能なエンドユーザであるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->creating_type->isDeletableCreatingType();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\ProductCollection
     */
    public function newCollection(array $models = []): EndUserCollection
    {
        return new EndUserCollection($models);
    }

    /**
     * エンドユーザに紐づく得意先マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_code');
    }

    /**
     * エンドユーザに紐づく納入先マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_destination(): BelongsTo
    {
        return $this->belongsTo(DeliveryDestination::class, 'delivery_destination_code');
    }

    /**
     * エンドユーザに紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function end_user_factories(): HasMany
    {
        return $this->hasMany(EndUserFactory::class, 'end_user_code', 'end_user_code');
    }

    /**
     * @return \App\ValueObjects\String\EndUserCode
     */
    public function getEndUserCodeAttribute($value): EndUserCode
    {
        return new EndUserCode($value);
    }

    /**
     * @return \App\ValueObjects\Date\ApplicationStartedOn
     */
    public function getApplicationStartedOnAttribute($value): ApplicationStartedOn
    {
        return new ApplicationStartedOn($value);
    }

    /**
     * @return \App\ValueObjects\Enum\CreatingType
     */
    public function getCreatingTypeAttribute($value): CreatingType
    {
        return new CreatingType($value);
    }
}
