<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class CorporationCode extends MasterCode
{
    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[0-9]+\z/";

    /**
     * @var int
     */
    protected const MAX_LENGTH = 6;

    /**
     * @var int
     */
    protected const MIN_LENGTH = 6;

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("%d桁ちょうどの半角数字が\n入力できます。", $this->getMaxLength());
    }
}
