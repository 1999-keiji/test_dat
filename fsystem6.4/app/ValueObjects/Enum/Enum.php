<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use App\ValueObjects\ValueObjectInterface;

abstract class Enum implements ValueObjectInterface, JsonSerializable
{
    /**
     * @var int $scalar
     */
    protected $scalar;

    /**
     * @param  mixed $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct($value = null)
    {
        if (! is_null($value) && ! static::isValidValue($value)) {
            throw new InvalidArgumentException("the value [{$value}] is not defined.");
        }

        $this->scalar = $value;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public static function isValidValue($value): bool
    {
        return in_array($value, static::ENUM, true);
    }

    /**
     * @return int|string
     */
    public function value()
    {
        return $this->scalar;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return array_flip(static::ENUM)[$this->value()];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->scalar;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return static::ENUM;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->all());
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value(),
            'label' => $this->label(),
        ];
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
