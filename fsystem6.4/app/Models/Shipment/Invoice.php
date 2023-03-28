<?php

namespace App\Models\Shipment;

use App\Models\Model;
use App\Models\Shipment\Collections\InvoiceCollection;
use App\Traits\AuthorObservable;
use App\ValueObjects\Date\DeliveryDate;

class Invoice extends Model
{
    use AuthorObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'invoice_number';

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
        'has_fixed' => 'boolean'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Shipment\Collections\InvoiceCollection
     */
    public function newCollection(array $models = []): InvoiceCollection
    {
        return new InvoiceCollection($models);
    }

    /**
     * 納入年月を取得
     *
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getDeliveryMonth(): DeliveryDate
    {
        return DeliveryDate::createFromYearMonth($this->delivery_month);
    }

    /**
     * 締め処理のされた請求書かどうか判定する
     *
     * @return bool
     */
    public function hasFixed(): bool
    {
        return ! is_null($this->invoice_number) && $this->has_fixed;
    }
}
