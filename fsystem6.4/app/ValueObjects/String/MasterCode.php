<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

use BadMethodCallException;
use InvalidArgumentException;
use App\ValueObjects\ValueObjectInterface;

abstract class MasterCode implements ValueObjectInterface
{
    /**
     * @var string $scalar
     */
    protected $scalar;

    /**
     * @var int
     */
    protected const MIN_LENGTH = 1;

    /**
     * @var int
     */
    protected const MAX_LENGTH = 15;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[a-zA-Z0-9_-]+\z/";

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

        $this->scalar = $value;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public static function isValidValue($value): bool
    {
        if (mb_strlen($value) < static::MIN_LENGTH) {
            return false;
        }
        if (mb_strlen($value) > static::MAX_LENGTH) {
            return false;
        }
        if (! preg_match(static::REGEX_PATTERN, $value)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->scalar;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->scalar;
    }

    /**
     * @return int
     */
    public function getMinLength(): int
    {
        return static::MIN_LENGTH;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return static::MAX_LENGTH;
    }

    /**
     * @return string
     */
    public function getRegexPattern(): string
    {
        return static::REGEX_PATTERN;
    }

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return $this->getMinLength() === $this->getMaxLength() ?
            sprintf("%d文字ちょうどの半角英数字、\nハイフン、アンダーバーが入力できます。", $this->getMaxLength()) :
            sprintf("%d文字以上%d文字以内の\n半角英数字、ハイフン、\nアンダーバーが入力できます。", $this->getMinLength(), $this->getMaxLength());
    }

    /**
     * @return void
     * @throws BadMethodCallException
     */
    public function __set($key, $value)
    {
        throw new BadMethodCallException('All setter is forbidden.');
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'min_length' => $this->getMinlength(),
            'max_length' => $this->getMaxlength(),
            'regex_pattern' => $this->getRegexPattern(),
            'help_text' => $this->getHelpText(),
        ]);
    }
}
