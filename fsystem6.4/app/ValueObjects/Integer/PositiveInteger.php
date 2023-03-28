<?php

declare(strict_types=1);

namespace App\ValueObjects\Integer;

use BadMethodCallException;
use JsonSerializable;
use InvalidArgumentException;
use App\ValueObjects\ValueObjectInterface;

abstract class PositiveInteger implements ValueObjectInterface, JsonSerializable
{
    /**
     * @var int $scalar
     */
    protected $scalar;

    /**
     * @var int
     */
    protected const MINIMUM_NUM  = 0;

    /**
     * @var int
     */
    protected const MAXIMUM_NUM = 999999999999999999;

    /**
     * @var int
     */
    protected const DECIMALS = 0;

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

        $this->scalar = (int)$value;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public static function isValidValue($value): bool
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);
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

        return true;
    }

    /**
     * @return int
     */
    public function value(): int
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
     * @var int
     */
    public function getMinimumNum(): int
    {
        return static::MINIMUM_NUM;
    }

    /**
     * @var int
     */
    public function getMaximumNum(): int
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
        return sprintf("%d桁以内の半角正整数が\n入力できます。", $this->getMaxLength());
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'minimum_num' => $this->getMinimumNum(),
            'maximum_num' => $this->getMaximumNum(),
            'max_length' => $this->getMaxLength(),
            'decimals' => $this->getDecimals(),
            'help_text' => $this->getHelpText(),
        ]);
    }

    /**
     * @return int
     */
    public function jsonSerialize(): int
    {
        return $this->value();
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
