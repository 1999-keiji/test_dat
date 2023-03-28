<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Shipment;

use Illuminate\View\View;
use App\Models\Stock\Stock;

class ProductAllocationsComposer
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
            'input_group_list' => config('constant.master.factory_products.input_group'),
            'warning_date_term_of_allocation' => Stock::getExpirationTerm()
        ]);
    }
}
