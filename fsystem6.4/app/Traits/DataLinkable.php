<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait DataLinkable
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('base_plus_delete_flag', function (Builder $builder) {
            $builder->where('base_plus_delete_flag', false)
                ->orWhereNull('base_plus_delete_flag');
        });
    }

    /**
     * BASE+連携項目を取得
     *
     * @return array
     */
    public function getLinkedColumns(): array
    {
        return self::LINKED_COLUMNS;
    }

    /**
     * BASE+連携項目であるため更新不可であるか判定
     *
     * @param  string $key
     * @return bool
     */
    public function isDisabledToUpdate(string $key): bool
    {
        return ! $this->creating_type->isUpdatableCreatingType() && in_array($key, $this->getLinkedColumns(), true);
    }

    /**
     * BASE+連携項目に対して、更新不可であることを示すHTML属性を付加する
     *
     * @param  string $key
     * @return string
     */
    public function addDisabledProp(string $key): string
    {
        return $this->isDisabledToUpdate($key) ? 'disabled' : '';
    }
}
