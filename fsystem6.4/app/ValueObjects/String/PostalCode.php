<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class PostalCode extends MasterCode
{
    /**
     * @var int
     */
    protected const MAX_LENGTH = 10;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[0-9-]+\z/";

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("%d文字以内の半角数字、\n半角ハイフンが入力できます。", $this->getMaxLength());
    }
}
