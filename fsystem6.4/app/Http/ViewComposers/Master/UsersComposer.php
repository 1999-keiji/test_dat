<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\ValueObjects\String\UserCode;
use App\ValueObjects\Enum\Affiliation;
use App\ValueObjects\Enum\Permission;

class UsersComposer
{
    /**
     * @var \App\Services\Master\FactoryService $factory_service
     */
    private $factory_service;

    /**
     *
     * @param \App\Services\Master\FactoryService $factory_service
     */
    public function __construct(FactoryService $factory_service)
    {
        $this->factory_service = $factory_service;
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
            'user_code' => new UserCode(),
            'affiliation' => new Affiliation(),
            'factories' => $this->factory_service->getAllFactories(),
            'permission' => new Permission()
        ]);
    }
}
