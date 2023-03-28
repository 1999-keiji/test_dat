<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\String\CorporationCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;

class CorporationsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $view->with([
            'corporation_code' => new CorporationCode(),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode()
        ]);
    }
}
