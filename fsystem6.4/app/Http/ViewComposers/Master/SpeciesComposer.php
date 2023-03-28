<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\String\CategoryCode;
use App\ValueObjects\String\SpeciesCode;

class SpeciesComposer
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
            'species_code' => new SpeciesCode(),
            'category_code' => new CategoryCode()
        ]);
    }
}
