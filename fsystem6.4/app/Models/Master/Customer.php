<?php

declare(strict_types=1);

namespace App\Models\Master;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\CustomerCollection;
use App\Models\Shipment\Invoice;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Enum\ClosingDate;
use App\ValueObjects\Enum\PaymentTimingDate;
use App\ValueObjects\Enum\RoundingType;
use App\ValueObjects\Enum\SlipStatusType;

class Customer extends Model
{
    use Sortable, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'customer_code';

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
        'is_default_customer',
        'created_by',
        'created_at'
    ];

    /**
     * @var array
     */
    public $sortbale = ['customer_code', 'customer_name'];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\CustomerCollection
     */
    public function newCollection(array $models = []): CustomerCollection
    {
        return new CustomerCollection($models);
    }

    /**
     * 削除可能な得意先であるか判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return ! $this->is_default_customer && $this->end_users->isEmpty();
    }

    /**
     * 伝票状態種別を取得
     *
     * @return int
     */
    public function getSlipStatusType(): int
    {
        return $this->order_cooperation ? SlipStatusType::TEMP_ORDER : SlipStatusType::FIXED_ORDER;
    }

    /**
     * 請求書の請求期限日を取得
     *
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getPaymentDate(DeliveryDate $delivery_month): DeliveryDate
    {
        $payment_date = $delivery_month->addMonth($this->payment_timing_month)->endOfMonth();
        if ($this->payment_timing_date !== PaymentTimingDate::END_OF_MONTH) {
            $payment_date = $payment_date->day($this->payment_timing_date);
        }

        return $payment_date;
    }

    /**
     * 小数の丸め演算用のSQL文字列を取得
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function getRoundingSql(): string
    {
        if ($this->rounding_type === RoundingType::FLOOR) {
            return 'FLOOR';
        }
        if ($this->rounding_type === RoundingType::CEIL) {
            return 'CEIL';
        }
        if ($this->rounding_type === RoundingType::ROUND) {
            return 'ROUND';
        }

        throw new InvalidArgumentException('invalid rounding type.');
    }

    /**
     * 締め日に応じて請求対象の注文データの納入日の初日を取得
     *
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getFirstOfDeliveryDateOfInvoice(DeliveryDate $delivery_month): DeliveryDate
    {
        if ($this->closing_date === ClosingDate::END_OF_MONTH) {
            return $delivery_month->firstOfMonth();
        }

        return $delivery_month->subMonth()->day($this->closing_date)->addDay();
    }

    /**
     * 締め日に応じて請求対象の注文データの納入日の末日を取得
     *
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getEndOfDeliveryDateOfInvoice(DeliveryDate $delivery_month): DeliveryDate
    {
        if ($this->closing_date === ClosingDate::END_OF_MONTH) {
            return $delivery_month->endOfMonth();
        }

        return $delivery_month->day($this->closing_date);
    }

    /**
     * 締め日に応じて請求対象の注文データの納入日の初日と末日を取得
     *
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @return array
     */
    public function getDeliveryDateTermOfInvoice(DeliveryDate $delivery_month): array
    {
        return [
            'from' => $this->getFirstOfDeliveryDateOfInvoice($delivery_month)->format('Y-m-d'),
            'to' => $this->getEndOfDeliveryDateOfInvoice($delivery_month)->format('Y-m-d')
        ];
    }

    /**
     * 指定された工場の最新の請求済の納入年月を取得
     *
     * @param  string $factory_code
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getLatestDeliveryMonthByFactory(string $factory_code): DeliveryDate
    {
        $delivery_month = $this->invoices
            ->filterByFactory($factory_code)
            ->filterFixed()
            ->sortByDeliveryMonthDesc()
            ->first()
            ->delivery_month ?? '1999/12';

        return DeliveryDate::createFromYearMonth($delivery_month);
    }

    /**
     * 得意先に紐づくエンドユーザマスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function end_users(): HasMany
    {
        return $this->hasMany(EndUser::class, 'customer_code');
    }

    /**
     * 得意先に紐づく請求書データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_code');
    }
}
