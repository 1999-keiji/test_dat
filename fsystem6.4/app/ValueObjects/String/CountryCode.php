<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class CountryCode extends MasterCode
{
    /**
     * @var int
     */
    protected const MIN_LENGTH = 2;

    /**
     * @var int
     */
    protected const MAX_LENGTH = 2;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[A-Z]+\z/";

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf('%d文字ちょうどの半角英大文字が入力できます。', $this->getMaxLength());
    }
}
