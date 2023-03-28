<?php

declare(strict_types=1);

namespace App\ValueObjects\Integer;

final class DeliveryLeadTime extends PositiveInteger
{
    /**
     * @var int
     */
    protected const MINIMUM_NUM  = 0;

    /**
     * @var int
     */
    protected const MAXIMUM_NUM = 9;

    /**
     * @var int
     */
    private const DEFAULT_DELIVERY_LEAD_TIME = 1;

    /**
     * デフォルトの配送リードタイムを取得
     *
     * @return \App\ValueObjects\Integer\DeliveryLeadTime
     */
    public function getDefaultDeliveryLeadTime(): DeliveryLeadTime
    {
        return new self(self::DEFAULT_DELIVERY_LEAD_TIME);
    }

    /**
     * サフィックスをつけて配送リードタイムを表示
     *
     * @return string
     */
    public function withSuffix(): string
    {
        return $this->value().trans('view.master.delivery_warehouses.delivery_lead_time_suffix');
    }

    /**
     * 日本語の構文で取得
     *
     * @return string
     */
    public function toJapaneseSyntax(): string
    {
        if ($this->value() === 0) {
            return trans('view.global.today');
        }
        if ($this->value() === 1) {
            return trans('view.global.tomorrow');
        }

        return $this->withSuffix();
    }
}
