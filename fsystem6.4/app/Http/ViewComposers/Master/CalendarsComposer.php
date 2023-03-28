<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\Enum\EventClass;

class CalendarsComposer
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
            'event_class_list' => (new EventClass())->all(),
        ]);
    }
}
