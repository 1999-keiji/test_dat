<?php

declare(strict_types=1);

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Model;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\EndUser;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Master\TransportCompany;
use App\Models\Shipment\InvoiceReceiptInfomationLog;
use App\Models\Shipment\ProductAllocation;
use App\Models\Shipment\ShipmentInfomationLog;
use App\Models\Stock\Stock;
use App\Models\Order\Collections\OrderCollection;
use App\Traits\AccessControllableWithFactories;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Enum\AllocationStatus;
use App\ValueObjects\Enum\BasisForRecordingSalesClass;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\FixedShippingSharingFlag;
use App\ValueObjects\Enum\PickupTypeClass;
use App\ValueObjects\Enum\PickupTypeCode;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\ProductClass;
use App\ValueObjects\Enum\RelatedOrderStatusType;
use App\ValueObjects\Enum\ShipmentStatus;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\SmallPeaceOfPeperTypeClass;
use App\ValueObjects\Enum\SmallPeaceOfPeperTypeCode;
use App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass;

class Order extends Model
{
    use AccessControllableWithFactories, AuthorObservable, UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'order_number';

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
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function newCollection(array $models = []): OrderCollection
    {
        return new OrderCollection($models);
    }

    /**
     * BASE+の注文番号を取得
     *
     * @return string
     */
    public function getBasePlusOrderNumber(): string
    {
        if (! $this->base_plus_order_number || ! $this->base_plus_order_chapter_number) {
            return '';
        }

        return implode('-', [$this->base_plus_order_number, $this->base_plus_order_chapter_number]);
    }

    /**
     * 出荷作業帳票に出力する品名を取得
     *
     * @return string
     */
    public function getProductNameOnShipmentFile(): string
    {
        return mb_convert_kana($this->product_name, 'K', 'UTF-8');
    }

    /**
     * 通常注文かどうか判定する
     *
     * @return bool
     */
    public function isNormalOrder(): bool
    {
        return $this->slip_type->value() === SlipType::NORMAL_SLIP;
    }

    /**
     * 確定注文かどうか判定する
     *
     * @return bool
     */
    public function isFixedOrder(): bool
    {
        return $this->slip_status_type->value() === SlipStatusType::FIXED_ORDER;
    }

    /**
     * 取消注文かどうか判定する
     *
     * @return bool
     */
    public function isCanceledOrder(): bool
    {
        return $this->process_class->value() === ProcessClass::CANCEL_PROCESS;
    }

    /**
     * 更新可能なデータかどうか判定する
     *
     * @return bool
     */
    public function isUpdatable(): bool
    {
        return $this->creating_type->isDeletableCreatingType() && $this->isNormalOrder();
    }

    /**
     * 工場キャンセル可能かどうか判定する
     *
     * @return bool
     */
    public function isCancelable(): bool
    {
        return ! $this->isAllocated() &&
            ! $this->hadBeenShipped() &&
            ! $this->isCanceledOrder() &&
            ! $this->factory_cancel_flag;
    }

    /**
     * 紐付け済注文かどうか判定する
     *
     * @return bool
     */
    public function isRelatedTemporaryOrder(): bool
    {
        return $this->slip_status_type->value() === SlipStatusType::RELATION_TEMP_ORDER;
    }

    /**
     * 紐づけ可能な仮注文かどうか判定する
     *
     * @return bool
     */
    public function isLinkableTemporaryOrder(): bool
    {
        return ! $this->isFixedOrder() &&
            ! $this->isCanceledOrder() &&
            ! $this->factory_cancel_flag &&
            ! $this->isRelatedTemporaryOrder();
    }

    /**
     * 紐づけ解除可能な確定注文かどうか判定する
     *
     * @return bool
     */
    public function isLinkCancelableFixedOrder(): bool
    {
        return $this->isFixedOrder() &&
            ! $this->hadBeenShipped() &&
            $this->related_order_status_type->value() === RelatedOrderStatusType::MANUAL_RELATED;
    }

    /**
     * 紐づけ可能な確定注文かどうか判定する
     *
     * @return bool
     */
    public function isLinkableFixedOrder(): bool
    {
        return $this->isFixedOrder() &&
            ! $this->isCanceledOrder() &&
            ! $this->factory_cancel_flag &&
            ! $this->hadBeenShipped() &&
            $this->related_order_status_type->value() === RelatedOrderStatusType::UN_RELATED;
    }

    /**
     * 紐づけられた仮注文を取得する
     *
     * @return \App\Models\Order\Order|null
     */
    public function getLinkedTemporaryOrder(): ?Order
    {
        if (! $this->isFixedOrder()) {
            return null;
        }

        $related_order = $this->related_order;
        if (is_null($related_order)) {
            return null;
        }

        $order = $related_order->temporary_order;
        $order->end_user_abbreviation = $order->end_user->end_user_abbreviation;
        $order->delivery_destination_abbreviation = $order->delivery_destination->delivery_destination_abbreviation;
        $order->formatted_order_unit = $order->formatOrderUnit();
        $order->formatted_order_amount = $order->formatOrderAmount();

        return $order;
    }

    /**
     * 紐づけられた確定注文を取得する
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function getLinkedFixedOrders(): OrderCollection
    {
        if ($this->isFixedOrder()) {
            return new OrderCollection();
        }

        $orders = $this->related_orders->map(function ($ro) {
            $order = $ro->fixed_order;
            $order->end_user_abbreviation = $order->end_user->end_user_abbreviation;
            $order->delivery_destination_abbreviation = $order->delivery_destination->delivery_destination_abbreviation;
            $order->formatted_order_unit = $order->formatOrderUnit();
            $order->formatted_order_amount = $order->formatOrderAmount();

            return $order;
        });

        return new OrderCollection($orders->all());
    }

    /**
     * 引当数量を取得する
     *
     * @return int
     */
    public function getAllocationQuantity(): int
    {
        $allocation_quantity = $this->allocation_quantity;
        if (is_null($allocation_quantity)) {
            $allocation_quantity = $this->product_allocations->toSumOfAllocationQuantity();
        }

        return (int)$allocation_quantity;
    }

    /**
     * 引当済にするのに必要な製品数を取得
     *
     * @return int
     */
    public function getProductQuantityToAllocateFull(): int
    {
        $number_of_cases = $this->number_of_cases;
        if (is_null($number_of_cases) && ! is_null($this->factory_product)) {
            $number_of_cases = $this->factory_product->number_of_cases;
        }
        if (is_null($number_of_cases)) {
            $number_of_cases = 1;
        }

        return $this->order_quantity * $number_of_cases;
    }

    /**
     * 引当実績があるかどうか判定する
     *
     * @return bool
     */
    public function isAllocated(): bool
    {
        return $this->getAllocationQuantity() !== 0;
    }

    /**
     * 引当済かどうか判定する
     *
     * @return bool
     */
    public function isFullAllocated(): bool
    {
        return $this->getProductQuantityToAllocateFull() === $this->getAllocationQuantity();
    }

    /**
     * 部分引当かどうか判定する
     *
     * @return bool
     */
    public function isPartAllocated(): bool
    {
        return $this->isAllocated() && ! $this->isFullAllocated();
    }

    /**
     * 出荷済かどうか判定する
     *
     * @return bool
     */
    public function hadBeenShipped(): bool
    {
        return ! is_null($this->fixed_shipping_at);
    }

    /**
     * 請求書発行済かどうか判定する
     *
     * @return bool
     */
    public function hadBeenIssuedInvoice(): bool
    {
        return ! is_null($this->invoice_number);
    }

    /**
     * 返品された注文かどうか判定する
     *
     * @return bool
     */
    public function isReturnedOrder(): bool
    {
        return ! is_null($this->returned_product);
    }

    /**
     * 紐づく工場商品が更新可能か判定する
     *
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return bool
     */
    public function canUpdateFactoryProduct(FactoryProduct $factory_product): bool
    {
        if ($this->factory_product_sequence_number === $factory_product->sequence_number) {
            return true;
        }

        return ! $this->isAllocated();
    }

    /**
     * 手動での注文変更時の伝票状態種別を取得する
     *
     * @param  array $params
     * @return int
     */
    public function getSlipStatusTypeOnManualUpdated(array $params): int
    {
        if ($this->customer->getSlipStatusType() === SlipStatusType::FIXED_ORDER) {
            return $this->customer->getSlipStatusType();
        }
        if (! $this->isFixedOrder()) {
            return $this->slip_status_type->value();
        }

        if ($this->delivery_date === $params['delivery_date'] &&
            $this->end_user_code === $params['end_user_code'] &&
            $this->delivery_destination_code === $params['delivery_destination_code'] &&
            $this->order_quantity === $params['order_quantity'] &&
            (float)$this->order_unit === (float)$params['order_unit'] &&
            $this->currency_code === $params['currency_code']) {
            return SlipStatusType::FIXED_ORDER;
        }

        return SlipStatusType::TEMP_ORDER;
    }

    /**
     * 集荷依頼書用の納入日を取得
     *
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getPrintingDeliveryDate(): DeliveryDate
    {
        $delivery_date = DeliveryDate::parse($this->delivery_date);

        $needs_to_subtract_printing_delivery_date = $this->needs_to_subtract_printing_delivery_date;
        if (is_null($needs_to_subtract_printing_delivery_date)) {
            $needs_to_subtract_printing_delivery_date = $this->delivery_destination
                ->needs_to_subtract_printing_delivery_date;
        }

        if ((bool)$needs_to_subtract_printing_delivery_date) {
            $delivery_date = $delivery_date->subDay();
        }

        return $delivery_date;
    }

    /**
     * 処理区分を文字列として取得
     *
     * @return string
     */
    public function getProcessClassLabel(): string
    {
        if ($this->factory_cancel_flag) {
            return (new ProcessClass(ProcessClass::CANCEL_PROCESS))->label();
        }

        return $this->process_class->label();
    }

    /**
     * 注文状態を文字列として取得
     *
     * @return string
     */
    public function getOrderStatus(): string
    {
        if ($this->isNormalOrder()) {
            return $this->slip_status_type->label();
        }

        return $this->slip_type->label();
    }

    /**
     * 単価をカンマ区切りで取得
     *
     * @return string
     */
    public function formatOrderUnit(): string
    {
        $order_unit_decimals = $this->order_unit_decimals;
        if (is_null($order_unit_decimals)) {
            $order_unit_decimals = $this->currency->order_unit_decimals;
        }

        return number_format((float)$this->order_unit, $order_unit_decimals);
    }

    /**
     * 合価をカンマ区切りで取得
     *
     * @return string
     */
    public function formatOrderAmount(): string
    {
        $order_amount_decimals = $this->order_amount_decimals;
        if (is_null($order_amount_decimals)) {
            $order_amount_decimals = $this->currency->order_amount_decimals;
        }

        return number_format((float)$this->order_amount, $order_amount_decimals);
    }

    /**
     * 受注単価をカンマ区切りで取得
     *
     * @return string
     */
    public function formatReceivedOrderUnit(): string
    {
        $order_unit_decimals = $this->order_unit_decimals;
        if (is_null($order_unit_decimals)) {
            $order_unit_decimals = $this->currency->order_unit_decimals;
        }

        return number_format((float)$this->received_order_unit, $order_unit_decimals);
    }

    /**
     * 得意先受注合価をカンマ区切りで取得
     *
     * @return string
     */
    public function formatReceivedOrderAmount(): string
    {
        $order_amount_decimals = $this->order_amount_decimals;
        if (is_null($order_amount_decimals)) {
            $order_amount_decimals = $this->currency->order_amount_decimals;
        }

        return number_format((float)$this->customer_received_order_amount, $order_amount_decimals);
    }

    /**
     * 返品単価をカンマ区切りで取得
     *
     * @return string
     */
    public function formatReturnedUnitPrice(): string
    {
        $returned_unit_price = $this->returned_unit_price;
        if (is_null($returned_unit_price) && ! is_null($this->returned_product)) {
            $returned_unit_price = $this->returned_product->unit_price;
        }

        return number_format(
            (float)$returned_unit_price,
            $this->order_unit_decimals ?: $this->currency->order_unit_decimals
        );
    }

    /**
     * 返品金額をカンマ区切りで取得
     *
     * @return string
     */
    public function formatReturnedAmount(): string
    {
        $returned_amount = $this->returned_amount;
        if (is_null($returned_amount) && ! is_null($this->returned_product)) {
            $returned_amount = $this->returned_product->unit_price * $this->returned_product->quantity;
        }

        return number_format(
            (float)$returned_amount,
            $this->order_amount_decimals ?: $this->currency->order_amount_decimals
        );
    }

    /**
     * 返品後の合価をカンマ区切りで取得
     *
     * @return string
     */
    public function formatAmountExceptReturned(): string
    {
        $amount_except_returned = $this->amount_except_returned;
        if (is_null($amount_except_returned)) {
            $returned_amount = $this->returned_amount;
            if (is_null($returned_amount) && ! is_null($this->returned_product)) {
                $returned_amount = $this->returned_product->unit_price * $this->returned_product->quantity;
            }

            $amount_except_returned = $this->order_amount - $returned_amount;
        }

        return number_format(
            (float)$amount_except_returned,
            $this->order_amount_decimals ?: $this->currency->order_amount_decimals
        );
    }

    /**
     * 出荷案内書出力回数を取得
     *
     * @return int
     */
    public function countShipmentInfomationLogs(): int
    {
        $count_shipment_infomation_logs = $this->count_shipment_infomation_logs;
        if (is_null($count_shipment_infomation_logs)) {
            $count_shipment_infomation_logs = $this->shipment_infomation_logs->count();
        }

        return (int)$count_shipment_infomation_logs;
    }

    /**
     * 出荷案内書出力履歴があるかどうか判定する
     *
     * @return bool
     */
    public function hasExportedShipmentInfomation(): bool
    {
        return $this->countShipmentInfomationLogs() !== 0;
    }

    /**
     * 納品受領書出力回数を取得
     *
     * @return int
     */
    public function countInvoiceReceiptInfomationLogs(): int
    {
        $count_invoice_receipt_infomation_logs = $this->count_invoice_receipt_infomation_logs;
        if (is_null($count_invoice_receipt_infomation_logs)) {
            $count_invoice_receipt_infomation_logs = $this->invoice_receipt_infomation_logs->count();
        }

        return (int)$count_invoice_receipt_infomation_logs;
    }

    /**
     * 納品受領書出力履歴があるかどうか判定する
     *
     * @return bool
     */
    public function hasExportedInvoiceReceiptInfomation(): bool
    {
        return $this->countInvoiceReceiptInfomationLogs() !== 0;
    }

    /**
     * 集荷依頼書の梱包数を取得
     *
     * @return int
     */
    public function getPackingQuantity(): ?int
    {
        $is_transportable_one_in_two = $this->is_transportable_one_in_two;
        if (is_null($is_transportable_one_in_two)) {
            $is_transportable_one_in_two = $this->factory_product->can_be_transported_double &&
                $this->transport_company->can_transport_double;
        }

        if (! (bool)$is_transportable_one_in_two) {
            return $this->order_quantity;
        }

        return (int)(ceil($this->order_quantity / 2));
    }

    /**
     * 納入日の取得
     *
     * @return \App\ValueObjects\Date\DeliveryDate
     */
    public function getDeliveryDate(): DeliveryDate
    {
        return DeliveryDate::parse($this->delivery_date);
    }

    /**
     * 出荷日の取得
     *
     * @return \App\ValueObjects\Date\ShippingDate
     */
    public function getShippingDate(): ShippingDate
    {
        return ShippingDate::parse($this->shipping_date);
    }

    /**
     * 出荷日から納入日までの日数を取得
     *
     * @return int
     */
    public function getDiffInDaysShippingAndDelivery(): int
    {
        return $this->getShippingDate()->diffInDays($this->getDeliveryDate());
    }

    /**
     * 出荷実績連携項目の取得
     *
     * @return array
     */
    public function getShippingLinkParams(): array
    {
        return [
            $this->own_company_code,
            $this->base_plus_order_number,
            $this->base_plus_order_chapter_number,
            $this->product_name,
            $this->product_code,
            $this->supplier_flag,
            $this->delivery_destination_code,
            $this->order_unit,
            $this->order_quantity,
            ShippingDate::parse($this->shipping_date)->format('Ymd'),
            $this->order_number,
            $this->order_number.'01',
            $this->order_message
        ];
    }

    /**
     * @return \App\ValueObjects\Enum\ProductClass
     */
    public function getProdcutClassAttribute($value): ProductClass
    {
        return new ProductClass($value);
    }

    /**
     * @return \App\ValueObjects\Enum\SlipType
     */
    public function getSlipTypeAttribute($value): SlipType
    {
        return new SlipType($value);
    }

    /**
     * @return \App\ValueObjects\Enum\SlipStatusType
     */
    public function getSlipStatusTypeAttribute($value): SlipStatusType
    {
        return new SlipStatusType($value);
    }

    /**
     * @return \App\ValueObjects\Enum\ProcessClass
     */
    public function getProcessClassAttribute($value): ProcessClass
    {
        return new ProcessClass($value);
    }

    /**
     * @return \App\ValueObjects\Enum\CreatingType
     */
    public function getCreatingTypeAttribute($value): CreatingType
    {
        return new CreatingType($value);
    }

    /**
     * @return \App\ValueObjects\Enum\SmallPeaceOfPeperTypeClass
     */
    public function getSmallPeaceOfPeperTypeClassAttribute($value): SmallPeaceOfPeperTypeClass
    {
        return new SmallPeaceOfPeperTypeClass($value);
    }

    /**
     * @return \App\ValueObjects\Enum\SmallPeaceOfPeperTypeCode
     */
    public function getSmallPeaceOfPeperTypeCodeAttribute($value): SmallPeaceOfPeperTypeCode
    {
        return new SmallPeaceOfPeperTypeCode($value);
    }

    /**
     * @return \App\ValueObjects\Enum\PickupTypeClass
     */
    public function getPickupTypeClassAttribute($value): ?PickupTypeClass
    {
        if ($value === '') {
            return null;
        }

        return new PickupTypeClass($value);
    }

    /**
     * @return \App\ValueObjects\Enum\PickupTypeCode
     */
    public function getPickupTypeCodeAttribute($value): ?PickupTypeCode
    {
        if ($value === '') {
            return null;
        }

        return new PickupTypeCode($value);
    }

    /**
     * @return \App\ValueObjects\Enum\BasisForRecordingSalesClass
     */
    public function getBasisForRecordingSalesClassAttribute($value): ?BasisForRecordingSalesClass
    {
        if ($value === '') {
            return null;
        }

        return new BasisForRecordingSalesClass($value);
    }

    /**
     * @return \App\ValueObjects\Enum\StatementDeliveryPriceDisplayClass
     */
    public function getStatementDeliveryPriceDisplayClassAttribute($value): ?StatementDeliveryPriceDisplayClass
    {
        if ($value === '') {
            return null;
        }

        return new StatementDeliveryPriceDisplayClass($value);
    }

    /**
     * @return \App\ValueObjects\Enum\RelatedOrderStatusType
     */
    public function getRelatedOrderStatusTypeAttribute($value): RelatedOrderStatusType
    {
        return new RelatedOrderStatusType($value);
    }

    /**
     * @return \App\ValueObjects\Enum\FixedShippingSharingFlag
     */
    public function getFixedShippingSharingFlagAttribute($value): FixedShippingSharingFlag
    {
        return new FixedShippingSharingFlag($value);
    }

    /**
     * @return \App\ValueObjects\Enum\AllocationStatus
     */
    public function getAllocationStatusAttribute(): AllocationStatus
    {
        if (! $this->isAllocated()) {
            return new AllocationStatus(AllocationStatus::UNALLOCATED);
        }
        if ($this->isFullAllocated()) {
            return new AllocationStatus(AllocationStatus::ALLOCATED);
        }

        return new AllocationStatus(AllocationStatus::PART_ALLOCATED);
    }

    /**
     * @return \App\ValueObjects\Enum\ShipmentStatus
     */
    public function getShipmentStatusAttribute(): ShipmentStatus
    {
        if ($this->hadBeenShipped()) {
            return new ShipmentStatus(ShipmentStatus::SHIPPED);
        }

        return new ShipmentStatus(ShipmentStatus::UNSHIPPED);
    }

    /**
     * 注文に紐づく得意先マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_code', 'customer_code');
    }

    /**
     * 注文に紐づくエンドユーザマスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function end_user(): BelongsTo
    {
        return $this->belongsTo(EndUser::class, 'end_user_code', 'end_user_code')
            ->where('application_started_on', '<=', $this->delivery_date)
            ->orderBy('application_started_on', 'DESC');
    }

    /**
     * 注文に紐づく納入先マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_destination(): BelongsTo
    {
        return $this->belongsTo(DeliveryDestination::class, 'delivery_destination_code', 'delivery_destination_code');
    }

    /**
     * 注文に紐づく工場マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_code', 'factory_code');
    }

    /**
     * 注文に紐づく工場取扱商品マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factory_product(): BelongsTo
    {
        return $this->belongsTo(FactoryProduct::class, 'factory_code', 'factory_code')
            ->where('sequence_number', $this->factory_product_sequence_number);
    }

    /**
     * 注文に紐づく運送会社マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transport_company(): BelongsTo
    {
        return $this->belongsTo(TransportCompany::class, 'transport_company_code', 'transport_company_code');
    }

    /**
     * 注文に紐づく通貨マスタの情報を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'currency_code');
    }

    /**
     * 注文に紐づく注文履歴データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_number');
    }

    /**
     * 注文に紐づく注文紐づけデータを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function related_order(): BelongsTo
    {
        return $this->belongsTo(RelatedOrder::class, 'order_number', 'fixed_order_number');
    }

    /**
     * 注文に紐づく注文紐づけデータを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function related_orders(): HasMany
    {
        return $this->hasMany(RelatedOrder::class, 'temporary_order_number', 'order_number');
    }

    /**
     * 注文に紐づく返品データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function returned_product(): HasOne
    {
        return $this->hasOne(ReturnedProduct::class, 'order_number');
    }

    /**
     * 注文に紐づく製品引当データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product_allocations(): HasMany
    {
        return $this->hasMany(ProductAllocation::class, 'order_number');
    }

    /**
     * 注文に紐づく出荷案内書出力ログを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shipment_infomation_logs(): HasMany
    {
        return $this->hasMany(ShipmentInfomationLogs::class, 'order_number');
    }

    /**
     * 注文に紐づく納品受領書出力ログを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_receipt_infomation_logs(): HasMany
    {
        return $this->hasMany(InvoiceReceiptInfomationLogs::class, 'order_number');
    }

    /**
     * 注文に紐づく在庫データを取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'order_number');
    }
}
