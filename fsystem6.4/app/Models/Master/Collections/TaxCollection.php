<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\DisabledToApplyTaxException;
use App\Models\Master\Tax;
use App\ValueObjects\Date\Date;

class TaxCollection extends Collection
{
    /**
     * 日付をもとに、適用される税率を取得
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Master\Tax $tax
     * @throws \App\Exceptions\DisabledToApplyTaxException
     */
    public function findAppliedTaxRate(Date $date): Tax
    {
        $tax = $this
            ->filter(function ($t) use ($date) {
                return $date->gte($t->getApplicationStartedOn());
            })
            ->sortByDesc(function ($t) {
                return $t->getApplicationStartedOn()->timestamp;
            })
            ->first();

        if (is_null($tax)) {
            throw new DisabledToApplyTaxException(
                'could not apply tax. date:'.$date
            );
        }

        return $tax;
    }
}
