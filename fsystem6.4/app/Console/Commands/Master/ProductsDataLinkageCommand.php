<?php

declare(strict_types=1);

namespace App\Console\Commands\Master;

use Illuminate\Console\Command;
use App\Services\Master\ProductService;

class ProductsDataLinkageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品マスタ自動連携';

    /**
     * @var \App\Services\Master\ProductService
     */
    private $product_service;

    /**
     * @param \App\Services\Master\ProductService $product_service
     *
     * @return void
     */
    public function __construct(ProductService $product_service)
    {
        parent::__construct();
        $this->product_service = $product_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->product_service->logInfo('datalink product：start.');
        $results = $this->product_service->datalinkProduct();
        $this->product_service->logInfo(
            'datalink product：end.',
            [$results
                ? config('constant.data_link.global.results.success')
                : config('constant.data_link.global.results.fail')
            ]
        );
    }
}
