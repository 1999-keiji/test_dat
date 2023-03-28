<?php

declare(strict_types=1);

namespace App\Console\Commands\Master;

use Illuminate\Console\Command;
use App\Services\Master\EndUserService;

class EndUsersDataLinkageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-end_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'エンドユーザマスタ自動連携';

    /**
     * @var \App\Services\Master\EndUserService
     */
    private $end_user_service;

    /**
     * @param \App\Services\Master\EndUserService $end_user_service
     *
     * @return void
     */
    public function __construct(EndUserService $end_user_service)
    {
        parent::__construct();

        $this->end_user_service = $end_user_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->end_user_service->logInfo('datalink end user：start.');
        $results = $this->end_user_service->datalinkEndUser();
        $this->end_user_service->logInfo(
            'datalink end user：end.',
            [$results
                ? config('constant.data_link.global.results.success')
                : config('constant.data_link.global.results.fail')
            ]
        );
    }
}
