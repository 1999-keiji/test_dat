<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class SymbolicCode extends MasterCode
{
    /**
     * @var int
     */
    protected const MIN_LENGTH = 3;

    /**
     * @var int
     */
    protected const MAX_LENGTH = 3;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[A-Z]+\z/";

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf('%d文字ちょうどの半角英数字が入力できます。', $this->getMaxLength());
    }
}
