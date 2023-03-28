<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\String\TransportCompanyCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;

class TransportCompaniesComposer
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
            'transport_company_code' => new TransportCompanyCode(),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode()
        ]);
    }
}
