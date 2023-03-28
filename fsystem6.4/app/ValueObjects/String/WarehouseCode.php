<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

final class WarehouseCode extends MasterCode
{
    /**
     * @var int
     */
    protected const MAX_LENGTH = 15;

    /**
     * @var string
     */
    protected const REGEX_PATTERN = "/\A[A-Z0-9]+\z/";

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("%d文字以内の半角英大文字、\n半角数字が入力できます。", $this->getMaxLength());
    }
}
