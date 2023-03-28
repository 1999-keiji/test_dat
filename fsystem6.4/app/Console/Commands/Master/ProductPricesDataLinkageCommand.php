<?php

declare(strict_types=1);

namespace App\Console\Commands\Master;

use Illuminate\Console\Command;
use App\Services\Master\ProductPriceService;

class ProductPricesDataLinkageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-product_prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品価格マスタ自動連携';

    /**
     * @var \App\Services\Master\ProductPriceService
     */
    private $product_price_service;

    /**
     * @param \App\Services\Master\ProductPriceService $product_price_service
     *
     * @return void
     */
    public function __construct(ProductPriceService $product_price_service)
    {
        parent::__construct();
        $this->product_price_service = $product_price_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->product_price_service->logInfo('datalink product price：start.');
        $results = $this->product_price_service->datalinkProductPrice();
        $this->product_price_service->logInfo(
            'datalink product price：end.',
            [$results
                ? config('constant.data_link.global.results.success')
                : config('constant.data_link.global.results.fail')
            ]
        );
    }
}
