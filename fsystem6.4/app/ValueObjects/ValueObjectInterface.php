<?php

namespace App\ValueObjects;

interface ValueObjectInterface
{
    /**
     * @return bool
     */
    public static function isValidValue($value);

    /**
     * @return mixed
     */
    public function value();

    /**
     * @return string
     */
    public function __toString();
}
