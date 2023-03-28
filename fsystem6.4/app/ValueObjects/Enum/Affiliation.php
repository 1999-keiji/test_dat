<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class Affiliation extends Enum
{
    public const VVF = 1;
    public const VVF_OTHER = 2;
    public const VVF_SALE = 3;
    public const FACTORY = 4;
    public const FACTORY_OTHER = 5;

    protected const ENUM = [
        'VVF（管理）' => self::VVF,
        'VVF（販売）' => self::VVF_SALE,
        'VVF（その他）' => self::VVF_OTHER,
        '工場（管理）' => self::FACTORY,
        '工場（その他）' => self::FACTORY_OTHER
    ];

    /**
     * @var array
     */
    private const BELONGS_TO_FACTORY = [self::FACTORY, self::FACTORY_OTHER];

    /**
     * 工場の所属であることを示す所属を返却する
     *
     * @return array
     */
    public function getAffiliationsOfFactory(): array
    {
        return self::BELONGS_TO_FACTORY;
    }

    /**
     * 工場の所属であることを示す所属かどうか判定する
     *
     * @param  int $affiliation
     * @return bool
     */
    public function belongsToFactory($affiliation = null): bool
    {
        return in_array($affiliation ?: $this->value(), $this->getAffiliationsOfFactory(), true);
    }
}
