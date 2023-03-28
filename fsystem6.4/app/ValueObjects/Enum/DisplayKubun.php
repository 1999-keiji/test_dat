<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class DisplayKubun extends Enum
{
    public const FIXED = 1;
    public const PROCESS = 2;

    protected const ENUM = [
        '確定表示'   => self::FIXED,
        '仕掛かり中' => self::PROCESS
    ];

    /**
     * 確定していることを示す状態かどうか判定する
     *
     * @return bool
     */
    public function isFixedStatus(): bool
    {
        return $this->value() === self::FIXED;
    }

    /**
     * JSON文字列のオプション形式に加工
     *
     * @return string
     */
    public function toJsonOptions(): string
    {
        $display_kubun_list = [];
        foreach (self::ENUM as $label => $value) {
            $display_kubun_list[] = [
                'label' => $label,
                'value' => $value,
                'selected' => $this->value() === $value
            ];
        }

        return json_encode($display_kubun_list);
    }
}
