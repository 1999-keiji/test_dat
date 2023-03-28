<?php

namespace App\Console\Commands\Stock;

use Illuminate\Console\Command;
use App\Extension\Logger\ApplicationLogger;
use App\Services\Stock\CarryOverStockService;
use App\Services\Stock\StockStateService;

class StockDatabatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:databatch-stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '在庫データバッチ';

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @var \App\Services\Stock\StockStateService
     */
    private $stock_state_service;

    /**
     * @var \App\Services\Stock\CarryOverStockService
     */
    private $carry_over_stock_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ApplicationLogger $application_logger,
        StockStateService $stock_state_service,
        CarryOverStockService $carry_over_stock_service
    ) {
        parent::__construct();

        $this->application_logger = $application_logger;
        $this->stock_state_service = $stock_state_service;
        $this->carry_over_stock_service = $carry_over_stock_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logInfo('stock batch process：start.');

        $this->stock_state_service->saveStockStates();
        $this->carry_over_stock_service->saveCarryOveredStocks();

        $this->logInfo('stock batch process：end.');
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
        $this->application_logger->info(
            $message,
            array_merge([$this->description], $contents)
        );
    }
}
