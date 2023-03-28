<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class Permission extends Enum
{
    public const FORBIDDEN = 0;
    public const READABLE = 1;
    public const WRITABLE = 2;

    protected const ENUM = [
        '権限なし' => self::FORBIDDEN,
        '参照' => self::READABLE,
        '登録' => self::WRITABLE
    ];

    /**
     * アクセス権限があるか判定する
     *
     * @return bool
     */
    public function canAccess(): bool
    {
        return $this->value() !== self::FORBIDDEN;
    }

    /**
     * データ保存権限があるか判定する
     *
     * @return bool
     */
    public function canSave(): bool
    {
        return $this->value() === self::WRITABLE;
    }
}
