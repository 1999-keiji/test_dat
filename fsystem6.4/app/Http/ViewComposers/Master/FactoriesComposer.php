<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\CorporationService;
use App\ValueObjects\Date\WorkingDate;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\String\FactoryCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;
use App\ValueObjects\String\SupplierCode;
use App\ValueObjects\String\SymbolicCode;

class FactoriesComposer
{
    /**
     * @var \App\Services\Master\CorporationService
     */
    private $corporation_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @return void
     */
    public function __construct(CorporationService $corporation_service)
    {
        $this->corporation_service = $corporation_service;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $view->with([
            'factory_code' => new FactoryCode(),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode(),
            'supplier_code' => new SupplierCode(),
            'symbolic_code' => new SymbolicCode(),
            'corporations' => $this->corporation_service->getAllCorporations(),
            'working_date' => new WorkingDate()
        ]);
    }
}
