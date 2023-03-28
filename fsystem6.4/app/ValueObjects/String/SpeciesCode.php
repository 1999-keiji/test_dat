<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class SpeciesCode extends MasterCode
{
    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[a-zA-Z0-9-_]+\z/";

    /**
     * @var int
     */
    protected const MAX_LENGTH = 15;

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("%d文字以内の半角英数字、\n半角ハイフン、半角アンダーバーが入力できます。", $this->getMaxLength());
    }
}
