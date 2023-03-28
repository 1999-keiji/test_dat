<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

use InvalidArgumentException;
use App\ValueObjects\ValueObjectInterface;

final class Password implements ValueObjectInterface
{
    /**
     * @var int
     */
    private const MIN_LENGTH = 8;

    /**
     * @var string
     */
    private const REGEX_PATTERN = "/\A[a-zA-Z0-9-]+\z/";

    /**
     * @var string
     */
    private $password;

    /**
     * @param  string $password
     */
    public function __construct($password = '')
    {
        if ($password !== '' && ! $this->isValidValue($password)) {
            throw new InvalidArgumentException('the password is invalid.');
        }

        if ($password === '') {
            $password = str_random(self::MIN_LENGTH);
        }

        $this->password = $password;
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public static function isValidValue($password): bool
    {
        if (mb_strlen($password) < self::MIN_LENGTH) {
            return false;
        }
        if (! preg_match(self::REGEX_PATTERN, $password)) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getMinLength(): int
    {
        return self::MIN_LENGTH;
    }

    /**
     * @return string
     */
    public function getRegexPattern(): string
    {
        return self::REGEX_PATTERN;
    }

    /**
     * @return string
     */
    public function hashPassword(): string
    {
        return bcrypt($this->password);
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->password;
    }
}
