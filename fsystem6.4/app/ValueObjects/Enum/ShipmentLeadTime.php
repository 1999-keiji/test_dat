<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class ShipmentLeadTime extends Enum
{
    public const TODAY = 0;
    public const NEXT_DAY = 1;

    /**
     * @var int
     */
    private const DEFAULT_SHIPMENT_LEAD_TIME = 1;

    protected const ENUM = [
        '当日' => self::TODAY,
        '翌日' => self::NEXT_DAY
    ];

    /**
     * デフォルトの出荷リードタイムを取得
     *
     * @return \App\ValueObjects\Enum\ShipmentLeadTime
     */
    public function getDefaultShipmentLeadTime(): ShipmentLeadTime
    {
        return new self(self::DEFAULT_SHIPMENT_LEAD_TIME);
    }

    /**
     * 出荷リードタイムが取りうる最大値を取得
     *
     * @return int
     */
    public function getMaximumShipmentLeadTime(): int
    {
        return self::NEXT_DAY;
    }

    /**
     * 当日出荷かどうか判定する
     *
     * @return bool
     */
    public function willShipOnTheDate(): bool
    {
        return $this->value() === self::TODAY;
    }
}
