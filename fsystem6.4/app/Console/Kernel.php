<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Master\EndUsersDataLinkageCommand;
use App\Console\Commands\Master\DeliveryDestinationsDataLinkCommand;
use App\Console\Commands\Master\ProductPricesDataLinkageCommand;
use App\Console\Commands\Master\ProductsDataLinkageCommand;
use App\Console\Commands\Master\SuppliersDataLinkageCommand;
use App\Console\Commands\Order\VVFBackboneImportDataLinkageCommand;
use App\Console\Commands\Plan\PanelStateDatabatchCommand;
use App\Console\Commands\Stock\StockDatabatchCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 自動連携コマンド
        $schedule->command(DeliveryDestinationsDataLinkCommand::class)->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command(EndUsersDataLinkageCommand::class)->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command(ProductsDataLinkageCommand::class)->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command(ProductPricesDataLinkageCommand::class)->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command(VVFBackboneImportDataLinkageCommand::class)->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command(SuppliersDataLinkageCommand::class)->everyThirtyMinutes()->withoutOverlapping();
        // データベースバッチ処理コマンド
        $schedule->command(PanelStateDatabatchCommand::class)->daily()->withoutOverlapping();
        $schedule->command(StockDatabatchCommand::class)->daily()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
