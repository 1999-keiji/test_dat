<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

use App\Models\Master\DeliveryDestination;
use App\Models\Master\Factory;

class DeliveryDate extends Date
{
    /**
     * @var int
     */
    private const WEEK_TERM_OF_EXPORT_ORDER_FORECAST = 8;

    /**
     * 納入日から出荷日を逆算して取得する
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  \App\Models\Master\Factory $factory
     * @return \App\ValueObjects\Date\ShippingDate
     */
    public function getShippingDate(DeliveryDestination $delivery_destination, Factory $factory): ShippingDate
    {
        $delivery_lead_time = $delivery_destination->getDeliveyLeadTimeByFactory($factory);
        return new ShippingDate($this->subDays($delivery_lead_time->value())->format(self::FORMAT));
    }

    /**
     * 受注フォーキャスト出力時の納入日の期間(週数)を取得
     *
     * @return int
     */
    public function getWeekTermOfExportOrderForecast(): int
    {
        return self::WEEK_TERM_OF_EXPORT_ORDER_FORECAST;
    }

    /**
     * 受注フォーキャスト出力用の納入日のリストを取得
     *
     * @return array
     */
    public function toListToExportOrderForecasts(): array
    {
        $delivery_date = $this;

        $list = [$delivery_date];
        $last_of_list = $delivery_date->addWeek($this->getWeekTermOfExportOrderForecast())->subDay();

        while (true) {
            if ($delivery_date->lt($last_of_list)) {
                $delivery_date = $delivery_date->addDay();

                $list[] = $delivery_date;
                continue;
            }

            break;
        }

        return $list;
    }
}
