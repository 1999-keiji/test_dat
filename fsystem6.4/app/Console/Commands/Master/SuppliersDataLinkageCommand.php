<?php

declare(strict_types=1);

namespace App\Console\Commands\Master;

use Illuminate\Console\Command;
use App\Services\Master\SupplierService;

class SuppliersDataLinkageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-suppliers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '仕入先マスタ自動連携';

    /**
     * @var \App\Services\Master\SupplierService
     */
    private $supplier_service;

    /**
     * @param \App\Services\Master\SupplierService $supplier_service
     *
     * @return void
     */
    public function __construct(SupplierService $supplier_service)
    {
        parent::__construct();
        $this->supplier_service = $supplier_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->supplier_service->logInfo('datalink supplier：start.');
        $results = $this->supplier_service->datalinkSupplier();
        $this->supplier_service->logInfo(
            'datalink supplier：end.',
            [$results
                ? config('constant.data_link.global.results.success')
                : config('constant.data_link.global.results.fail')
            ]
        );
    }
}
