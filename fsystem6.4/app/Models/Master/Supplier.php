<?php

declare(strict_types=1);

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\SupplierCollection;
use App\Traits\AuthorObservable;
use App\Traits\DataLinkable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\CreatingType;

class Supplier extends Model
{
    use Sortable, DataLinkable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['supplier_code', 'application_started_on'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;

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
        'export_target_flag' => 'boolean',
        'group_company_flag' => 'boolean',
        'can_display' => 'boolean'
    ];

    /**
     * @var array
     */
    public $sortbale = ['end_user_code', 'customer_code'];

    /**
     * @var array
     */
    private const LINKED_COLUMNS = [
        'supplier_code',
        'application_started_on',
        'creating_type',
        'supplier_name',
        'supplier_name2',
        'supplier_abbreviation',
        'supplier_name_kana',
        'supplier_name_english',
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
        'supplier_staff_name',
        'currency_code',
        'supplier_class',
        'export_target_flag',
        'group_company_flag',
        'company_code',
        'company_name',
        'company_abbreviation',
        'company_name_kana',
        'company_name_english',
        'company_group_code',
        'company_group_name',
        'company_group_name_english',
        'can_display',
        'remark'
    ];

    /**
     * 削除可能な仕入先であるか判定
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
     * @return \App\Models\Master\Collections\SupplierCollection
     */
    public function newCollection(array $models = []): SupplierCollection
    {
        return new SupplierCollection($models);
    }

    /**
     * @return \App\ValueObjects\Enum\CreatingType
     */
    public function getCreatingTypeAttribute($value): CreatingType
    {
        return new CreatingType($value);
    }
}
