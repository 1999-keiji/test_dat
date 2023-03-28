<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\SpeciesService;
use App\ValueObjects\Decimal\ProductSize;
use App\ValueObjects\Decimal\ProductWeight;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ProductClass;
use App\ValueObjects\Integer\SalesOrderUnitQuantity;
use App\ValueObjects\String\CategoryCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\ProductCode;

class ProductsComposer
{
    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @var \App\ValueObjects\Enum\ProductClass
     */
    private $product_class;

    /**
     * @param  \App\Services\Master\SpeciesService $species_service
     * @param  \App\ValueObjects\Enum\ProductClass $product_class
     * @return void
     */
    public function __construct(SpeciesService $species_service, ProductClass $product_class)
    {
        $this->species_service = $species_service;
        $this->product_class = $product_class;
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
            'species' => $this->species_service->getAllSpecies(),
            'product_code' => new ProductCode(),
            'creating_type' => new CreatingType(CreatingType::MANUAL_CREATED),
            'category_code' => new CategoryCode(),
            'product_class_list' => $this->product_class->all(),
            'sales_order_unit_quantity' => new SalesOrderUnitQuantity(),
            'product_weight' => new ProductWeight(),
            'product_size' => new ProductSize(),
            'country_code' => new CountryCode()
        ]);
    }
}
