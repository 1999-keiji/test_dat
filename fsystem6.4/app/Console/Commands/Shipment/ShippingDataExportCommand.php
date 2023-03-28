<?php

namespace App\Console\Commands\Shipment;

use Illuminate\Console\Command;
use App\Extension\Logger\ApplicationLogger;
use App\Services\Order\OrderService;

class ShippingDataExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-shipping_data_export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '出荷データ送信';

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $logger;

    /**
     * @var \App\Services\Order\OrderService
     */
    private $order_service;

    /**
     * @param  \App\Extension\Logger\ApplicationLogger $logger
     * @param  \App\Services\Order\OrderService $order_service
     * @return void
     */
    public function __construct(ApplicationLogger $logger, OrderService $order_service)
    {
        parent::__construct();

        $this->logger = $logger;
        $this->order_service = $order_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logInfo('datalink shipping data export：start.');

        $result = $this->order_service->exportOrdersThatFixedShipping();

        $config = config('constant.data_link.global.results');
        $this->logInfo(
            'datalink shipping data export：end.',
            [$result ? $config['success'] : $config['fail']]
        );
    }

    /**
     * ログ出力
     *
     * @param  string $message
     * @param  array $contents
     * @return void
     */
    private function logInfo(string $message, array $contents = []): void
    {
        $this->logger->info(
            $message,
            array_merge([config('settings.data_link.shipment.shipping_data_export.program_name')], $contents)
        );
    }
}
