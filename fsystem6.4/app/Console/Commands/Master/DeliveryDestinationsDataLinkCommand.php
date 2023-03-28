<?php

declare(strict_types=1);

namespace App\Console\Commands\Master;

use Illuminate\Console\Command;
use App\Services\Master\DeliveryDestinationService;

class DeliveryDestinationsDataLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-delivery_destinations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '納入先マスタ自動連携';

    /**
     * @var \App\Services\Master\DeliveryDestinationService
     */
    private $delivery_destination_service;

    /**
     * @param \App\Services\Master\DeliveryDestinationService $delivery_destination_service
     *
     * @return void
     */
    public function __construct(DeliveryDestinationService $delivery_destination_service)
    {
        parent::__construct();

        $this->delivery_destination_service = $delivery_destination_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->delivery_destination_service->logInfo('datalink delivery destination：start.');
        $results = $this->delivery_destination_service->datalinkDeliveryDestination();
        $this->delivery_destination_service->logInfo(
            'datalink delivery destination：end.',
            [$results
                ? config('constant.data_link.global.results.success')
                : config('constant.data_link.global.results.fail')
            ]
        );
    }
}
