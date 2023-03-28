<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class CreatingType extends Enum
{
    public const BASE_PLUS_LINKED = 1;
    public const MANUAL_CREATED = 2;

    protected const ENUM = [
        'BASE+連携' => self::BASE_PLUS_LINKED,
        '手動登録' => self::MANUAL_CREATED
    ];

    private const UPDATABLE_CREATING_TYPES = [self::MANUAL_CREATED];

    private const DELETABLE_CREATING_TYPES = [self::MANUAL_CREATED];

    /**
     * 更新可能な作成種別を返却する
     *
     * @return array
     */
    public function getUpdatableCreatingTypes(): array
    {
        return self::UPDATABLE_CREATING_TYPES;
    }

    /**
     * 更新可能な作成種別であるかどうか判定する
     *
     * @return bool
     */
    public function isUpdatableCreatingType(): bool
    {
        return in_array($this->value(), $this->getUpdatableCreatingTypes(), true);
    }

    /**
     * 削除可能な作成種別を返却する
     *
     * @return array
     */
    public function getDeletableCreatingTypes(): array
    {
        return self::DELETABLE_CREATING_TYPES;
    }

    /**
     * 削除可能な作成種別であるかどうか判定する
     *
     * @return bool
     */
    public function isDeletableCreatingType(): bool
    {
        return in_array($this->value(), $this->getDeletableCreatingTypes(), true);
    }
}
