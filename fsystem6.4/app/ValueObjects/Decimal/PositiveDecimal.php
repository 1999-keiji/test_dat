<?php

declare(strict_types=1);

namespace App\ValueObjects\Decimal;

use BadMethodCallException;
use InvalidArgumentException;
use App\ValueObjects\ValueObjectInterface;

abstract class PositiveDecimal implements ValueObjectInterface
{
    /**
     * @var float $scalar
     */
    protected $scalar;

    /**
     * @var float
     */
    protected const MINIMUM_NUM  = 0;

    /**
     * @var float
     */
    protected const MAXIMUM_NUM = 999999999.99999;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A([1-9][0-9]{0,9}|0)(\.[0-9]{1,5})?\z/";

    /**
     * @var int
     */
    protected const DECIMALS = 5;

    /**
     * @param  mixed $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct($value = null)
    {
        if (! is_null($value) && ! static::isValidValue($value)) {
            throw new InvalidArgumentException("the value [{$value}] is invalid.");
        }

        $this->scalar = (float)$value;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public static function isValidValue($value): bool
    {
        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($value === false) {
            return false;
        }
        if ($value < 0) {
            return false;
        }
        if ($value < static::MINIMUM_NUM) {
            return false;
        }
        if ($value > static::MAXIMUM_NUM) {
            return false;
        }
        if (! preg_match(static::REGEX_PATTERN, (string)$value)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return number_format($this->scalar, static::DECIMALS);
    }

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->scalar;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->scalar;
    }

    /**
     * @var float
     */
    public function getMinimumNum(): float
    {
        return static::MINIMUM_NUM;
    }

    /**
     * @var float
     */
    public function getMaximumNum(): float
    {
        return static::MAXIMUM_NUM;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return strlen((string)$this->getMaximumNum());
    }

    /**
     * @return string
     */
    public function getRegexPattern(): string
    {
        return static::REGEX_PATTERN;
    }

    /**
     * @return int
     */
    public function getDecimals(): int
    {
        return static::DECIMALS;
    }

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf(
            "小数点も含めて%d桁以内の\n半角正数値が入力できます。\n小数点以下は%d桁まで\n入力できます。",
            $this->getMaxLength(),
            $this->getDecimals()
        );
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'min' => $this->getMinimumNum(),
            'max' => $this->getMaximumNum(),
            'max_length' => $this->getMaxLength(),
            'regex' => $this->getRegexPattern(),
            'decimals' => $this->getDecimals(),
            'help_text' => $this->getHelpText()
        ]);
    }

    /**
     * @return void
     * @throws BadMethodCallException
     */
    public function __set($key, $value)
    {
        throw new BadMethodCallException('All setter is forbidden.');
    }
}
