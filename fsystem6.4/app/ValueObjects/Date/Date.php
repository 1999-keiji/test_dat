<?php

declare(strict_types=1);

namespace App\ValueObjects\Date;

use InvalidArgumentException;
use JsonSerializable;
use Cake\Chronos\Date as BaseDate;
use App\Models\Master\Factory;
use App\ValueObjects\ValueObjectInterface;

abstract class Date extends BaseDate implements JsonSerializable, ValueObjectInterface
{
    /**
     * @var string
     */
    protected const FORMAT = 'Y/m/d';

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[0-9]{4}-[0-9]{2}-[0-9]{2}\z/";

    /**
     * @var string
     */
    protected const REGEX_OTHER_PATTERN = "/\A[0-9]{4}\/[0-9]{2}\/[0-9]{2}\z/";

    /**
     * @var array
     */
    protected const DAY_OF_THE_WEEKS = [
        self::MONDAY => '月',
        self::TUESDAY => '火',
        self::WEDNESDAY => '水',
        self::THURSDAY => '木',
        self::FRIDAY => '金',
        self::SATURDAY => '土',
        self::SUNDAY => '日'
    ];

    /**
     * @var string
     */
    private const COLOR_DEFAULT  = '#000000';

    /**
     * @var string
     */
    private const COLOR_SATURDAY = '#2685ca';

    /**
     * @var string
     */
    private const COLOR_SUNDAY   = '#FF0000';

    /**
     * @param string|null $time Fixed or relative time
     */
    public function __construct($time = 'now')
    {
        if ($time !== 'now' && ! static::isValidValue($time)) {
            throw new InvalidArgumentException("the time [{$time}] is invalid");
        }

        parent::__construct($time);
    }

    /**
     * @param  string $time
     * @return bool
     */
    public static function isValidValue($time): bool
    {
        return is_string($time) &&
            (preg_match(self::REGEX_PATTERN, $time) || preg_match(self::REGEX_OTHER_PATTERN, $time));
    }

    /**
     * Prepare the object for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->format(self::FORMAT);
    }

    /**
     * 年月から生成
     *
     * @param  string $year_month
     * @return \App\ValueObjects\Date\Date
     */
    public static function createFromYearMonth(string $year_month): Date
    {
        return new static($year_month.'/01');
    }

    /**
     * @return string
     */
    public function dayOfWeekJa(): string
    {
        return self::DAY_OF_THE_WEEKS[$this->format('N')];
    }

    /**
     * @return string
     */
    public function formatWithDayOfWeek(): string
    {
        return $this->value().'('. $this->dayOfWeekJa() .')';
    }

    /**
     * @return string
     */
    public function formatShortWithDayOfWeek(): string
    {
        return $this->format('n/j').'('. $this->dayOfWeekJa() .')';
    }

    /**
     * @return string
     */
    public function formatToJa(): string
    {
        return $this->format('Y年m月d日').'('. $this->dayOfWeekJa() .')';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format(self::FORMAT);
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return self::FORMAT;
    }

    /**
     * 前週の週明け日付を取得
     *
     * @return \App\ValueObjects\Date
     */
    public function toPreviousStartOfWeek(): Date
    {
        return $this->startOfweek()->subWeek();
    }

    /**
     * 今週の週明け日付を取得
     *
     * @return \App\ValueObjects\Date
     */
    public function toCurrentStartOfWeek(): Date
    {
        return $this->startOfweek();
    }

    /**
     * 今週の週終わり日付を取得
     *
     * @return \App\ValueObjects\Date
     */
    public function toCurrentEndOfWeek(): Date
    {
        return $this->startOfWeek()->addWeek()->subDay();
    }

    /**
     * 翌週の週明け日付を取得
     *
     * @return \App\ValueObjects\Date
     */
    public function toNextStartOfWeek(): Date
    {
        return $this->startOfweek()->addWeek();
    }

    /**
     * 月の初日から末日までのリストを取得する
     *
     * @return array
     */
    public function toListOfDatesOfTheMonth(): array
    {
        $date = $this->startOfMonth();
        $end_of_month = $this->endOfMonth();

        $list = [];
        while ($date->lte($end_of_month)) {
            $list[] = $date;
            $date = $date->addDay();
        }

        return $list;
    }

    /**
     * システム日付以前かどうか判定する
     *
     * @return bool
     */
    public function isPassedDate(): bool
    {
        return BaseDate::today()->gt($this);
    }

    /**
     * 曜日色を取得する
     *
     * @return string
     */
    public function getDayOfWeekColor(): string
    {
        if ($this->isSaturday()) {
            return self::COLOR_SATURDAY;
        }
        if ($this->isSunday()) {
            return self::COLOR_SUNDAY;
        }

        return self::COLOR_DEFAULT;
    }

    /**
     * 曜日をすべて取得する
     *
     * @return array
     */
    public static function getDayOfTheWeeks(): array
    {
        return self::DAY_OF_THE_WEEKS;
    }

    /**
     * 生販管理上、注文(実績)データを表示する日付かどうかを判定
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return bool
     */
    public function willDisplayOrderOnTheDate(?Date $date): bool
    {
        if ($this->isPassedDate()) {
            return true;
        }
        if (is_null($date)) {
            return false;
        }

        return $this->lte($date);
    }

    /**
     * 生販管理上、注文(実績)データを表示する年月かどうかを判定
     *
     * @return bool
     */
    public function willDisplayOrderOnTheMonth(): bool
    {
        return BaseDate::today()->firstOfMonth()->gte($this);
    }

    /**
     * 工場営業日かどうか判定する
     *
     * @param  \App\Models\Master\Factory $factory
     * @return bool
     */
    public function isWorkingDay(Factory $factory): bool
    {
        return in_array((int)$this->format('w'), $factory->getWorkingDayOfTheWeeks(), true);
    }
}
