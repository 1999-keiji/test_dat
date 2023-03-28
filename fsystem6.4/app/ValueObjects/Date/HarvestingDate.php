<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

class HarvestingDate extends Date
{
    /**
     * @var array
     */
    private const LABEL_COLORS = [
        ['#ff3300', '#0099cc', '#ffff66', '#66ff33', '#ff66ff', '#ffcc00', '#66ffff'],
        ['#ff3300', '#0099cc', '#ffff66', '#66ff33', '#ff66ff', '#ffcc00', '#66ffff']
    ];

    /**
     * @var int
     */
    private const OUTPUT_WEEK_TERM_OF_GROWTH_SALE_MANAGEMENT = 4;

    /**
     * @var bool
     */
    private $slide_to_start_of_week = true;

    /**
     * 収穫日を週初めにスライドするかどうかのフラグを反転する
     *
     * @return \App\ValueObjects\Date\HarvestingDate
     */
    public function toggleFlagOfSlidingToStartOfWeek(): HarvestingDate
    {
        $this->slide_to_start_of_week = ! $this->slide_to_start_of_week;
        return $this;
    }

    /**
     * 指定された期間だけ収穫日のリストを作成して返却
     *
     * @param  int $week_term
     * @return array
     */
    public function toListOfDate(int $week_term): array
    {
        $list = [];

        $harvesting_date = $this;
        if ($this->slide_to_start_of_week) {
            $harvesting_date = $this->toCurrentStartOfWeek();
        }

        $last_of_list = $harvesting_date->addWeek($week_term)->subDay();
        while ($harvesting_date->lte($last_of_list)) {
            $list[] = $harvesting_date;
            $harvesting_date = $harvesting_date->addDay();
        }

        return $list;
    }

    /**
     * 指定された期間だけ収穫日のリストを作成して返却
     * ※ 週ごとに分割
     *
     * @param  int $week_term
     * @return array
     */
    public function toListOfDatePerWeek(int $week_term): array
    {
        $list = [];

        $harvesting_date = $this;
        if ($this->slide_to_start_of_week) {
            $harvesting_date = $this->toCurrentStartOfWeek();
        }

        $last_of_list = $harvesting_date->addWeek($week_term)->subDay();
        while ($harvesting_date->lte($last_of_list)) {
            if (! array_key_exists($harvesting_date->format('W'), $list)) {
                $list[$harvesting_date->format('W')] = [];
            }

            $list[$harvesting_date->format('W')][] = $harvesting_date;
            $harvesting_date = $harvesting_date->addDay();
        }

        return $list;
    }

    /**
     * 1年分の収穫年月のリストを作成して返却
     *
     * @return array
     */
    public function toListOfMonth(): array
    {
        $list = [];
        $harvesting_month = $this->firstOfMonth();

        $last_of_list = $harvesting_month->addYear()->subMonth();
        while ($harvesting_month->lte($last_of_list)) {
            $list[] = $harvesting_month;
            $harvesting_month = $harvesting_month->addMonth();
        }

        return $list;
    }

    /**
     * 在庫引当のための収穫日のリストを返却
     *
     * @return array
     */
    public function toListOfHarvestingDatesToAllocateProducts(): array
    {
        $list = $this->subWeek()->toListOfDatePerWeek(count(self::LABEL_COLORS));
        return array_values($list);
    }

    /**
     * 収穫日から算出される在庫引当のための出荷日のリストを返却
     *
     * @return array
     */
    public function toListOfShippingDatesToAllocateProducts(): array
    {
        $list = [];

        $harvesting_date = $this;
        if ($this->slide_to_start_of_week) {
            $harvesting_date = $this->toCurrentStartOfWeek();
        }

        $last_of_list = $harvesting_date->toNextStartOfWeek();
        while ($harvesting_date->lte($last_of_list)) {
            $list[] = ShippingDate::parse((string)$harvesting_date);
            $harvesting_date = $harvesting_date->addDay();
        }

        return $list;
    }

    /**
     * 収穫日から算出されるデフォルトの出荷日を返却
     *
     * @param  bool $as_string
     * @return \App\ValueObjects\Date\ShippingDate
     */
    public function getDefaultShippingDate(): ShippingDate
    {
        return new ShippingDate($this->addDay()->value());
    }

    /**
     * 収穫日ごとのラベル色を取得
     *
     * @return array
     */
    public function getLabelColors(): array
    {
        return self::LABEL_COLORS;
    }

    /**
     * 生販管理表出力時の週期間を取得
     *
     * @return int
     */
    public function getOutputWeekTermOfGrowthSaleManagement(): int
    {
        return self::OUTPUT_WEEK_TERM_OF_GROWTH_SALE_MANAGEMENT;
    }

    /**
     * 生販管理表出力時の日数を取得
     *
     * @return int
     */
    public function getOutputDateTermOfGrowthSaleManagement(): int
    {
        return self::DAYS_PER_WEEK * $this->getOutputWeekTermOfGrowthSaleManagement();
    }

    /**
     * 生販管理表出力時の収穫日の末日を取得
     *
     * @return \App\ValueObjects\Date\HarvestingDate
     */
    public function getEndOfDateOfGrowthSaleManagement(): HarvestingDate
    {
        return $this->addWeeks($this->getOutputWeekTermOfGrowthSaleManagement());
    }

    /**
     * Prepare the object for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'date' => $this->value(),
            'day_of_the_week_ja' => $this->dayOfWeekJa()
        ];
    }
}
