<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Stock;

use Illuminate\View\View;
use App\Services\Master\FactoryService;
use App\ValueObjects\Date\WorkingDate;
use App\ValueObjects\Enum\AllocationStatus;
use App\ValueObjects\Enum\DisposalStatus;
use App\ValueObjects\Enum\StockStatus;

class StocksComposer
{
   /**
    * @var \App\Services\Master\FactoryService
    */
    private $factory_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @return void
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
            'factories' => $this->factory_service->getAllFactories(),
            'allocation_status_list' => AllocationStatus::getAllocationStatusExceptPartAllocated(),
            'input_group_list' => config('constant.master.factory_products.input_group'),
            'stock_status_list' => (new StockStatus())->all(),
            'disposal_status_list_except_disposal' => DisposalStatus::getDisposalStatusExceptDisposal(),
            'disposal_status_list_except_part_disposal' => DisposalStatus::getDisposalStatusExceptPartDisposal(),
            'working_date' => WorkingDate::now()
        ]);
    }
}
