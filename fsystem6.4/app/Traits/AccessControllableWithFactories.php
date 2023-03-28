<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait AccessControllableWithFactories
{
    /**
     * 所属中の工場によって取得するデータを制限
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $table
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAffiliatedFactories(Builder $query, $table = ''): Builder
    {
        $user = Auth::user();
        if (! $user->belongsToFactory()) {
            return $query;
        }

        $column = 'factory_code';
        if ($table !== '') {
            $column = "{$table}.{$column}";
        }

        return $query->whereIn($column, $user->getAffilicatedFactories()->pluck('factory_code')->all());
    }
}
