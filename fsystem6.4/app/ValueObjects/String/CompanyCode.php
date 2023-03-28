<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class CompanyCode extends MasterCode
{
    /**
     * @var int
     */
    protected const MAX_LENGTH = 6;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "";

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("%d文字以内の半角英大文字、\n半角数字が入力できます。", $this->getMaxLength());
    }
}
