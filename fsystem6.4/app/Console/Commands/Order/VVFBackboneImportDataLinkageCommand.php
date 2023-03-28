<?php

declare(strict_types=1);

namespace App\Console\Commands\Order;

use PDOException;
use ZipArchive;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Cake\Chronos\Chronos;
use App\Exceptions\DataLinkException;
use App\Extension\Logger\ApplicationLogger;
use App\Services\Order\VVFBackboneImportService;

class VVFBackboneImportDataLinkageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:datalink-vvf_backbone_import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'VVF基幹発注データ取込処理';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $file;

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $logger;

    /**
     * @var \App\Services\Order\VVFBackboneImportService
     */
    private $vvf_backbone_import_service;

    /**
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @param  \App\Extension\Logger\ApplicationLogger $logger
     * @param  \App\Services\Order\VVFBackboneImportService $vvf_backbone_import_service
     * @return void
     */
    public function __construct(
        Filesystem $file,
        ApplicationLogger $logger,
        VVFBackboneImportService $vvf_backbone_import_service
    ) {
        parent::__construct();

        $this->file = $file;
        $this->logger = $logger;
        $this->vvf_backbone_import_service = $vvf_backbone_import_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logInfo('datalink vvf backbone import：start.');
        $config = config('settings.data_link.order.orderings');

        $skipped = [];
        if ($this->file->exists($config['skipped_rows_file_path'])) {
            $result = $this->vvf_backbone_import_service->parseTransferedFile($config['skipped_rows_file_path']);
            $skipped = array_merge($skipped, $result['skipped']);
            try {
                $this->vvf_backbone_import_service->importTransferedOrders($result['rows']);
            } catch (PDOException $e) {
                report($e);
                if (count($skipped) !== 0) {
                    $this->file->put($config['skipped_rows_file_path'], implode("\r\n", $skipped));
                }

                exit;
            }

            $this->file->delete($config['skipped_rows_file_path']);
        }

        $end_files = [];
        foreach (glob($config['end_file_path_rule']) as $end_file) {
            $end_files[] = $end_file;
        }

        if (count($end_files) === 0) {
            $this->logInfo('datalink vvf backbone import：end.', [config('constant.data_link.global.results.success')]);
            exit;
        }

        sort($end_files);
        $this->logInfo('datalink vvf backbone import：end file existence check.');

        foreach ($end_files as $end_file_path) {
            $tsv_file_path = str_replace('end', 'txt', $end_file_path);
            if (! $this->file->exists($tsv_file_path)) {
                report(new DataLinkException(
                    config('constant.data_link.global.error_list.tsv_file_not_found').
                    "(file_path: ${tsv_file_path})"
                ));

                continue;
            }

            $result = $this->vvf_backbone_import_service->parseTransferedFile($tsv_file_path, $skipped);
            $skipped = array_merge($skipped, $result['skipped']);
            try {
                $this->vvf_backbone_import_service->importTransferedOrders($result['rows']);
            } catch (PDOException $e) {
                report($e);
                if (count($skipped) !== 0) {
                    $this->file->put($config['skipped_rows_file_path'], implode("\r\n", $skipped));
                }

                exit;
            }
        }

        $zip = new ZipArchive();
        $zip_file_name = sprintf(
            $config['zip_file_path'].$config['zip_file_name'],
            Chronos::now()->format('YmdHis')
        );

        $zip->open($zip_file_name, ZipArchive::CREATE);
        foreach ($end_files as $end_file_path) {
            $zip->addFile(str_replace('end', 'txt', $end_file_path));
        }

        $zip->close();
        foreach ($end_files as $end_file_path) {
            $this->file->delete($end_file_path, str_replace('end', 'txt', $end_file_path));
        }

        foreach ($this->file->allFiles($config['zip_file_path']) as $file) {
            $last_modified = Chronos::createFromTimestamp($this->file->lastModified($file));
            if (! $last_modified->wasWithinLast(config('settings.data_link.global.storage_period'))) {
                $this->file->delete($file);
            }
        }

        if (count($skipped) !== 0) {
            $this->file->put($config['skipped_rows_file_path'], implode("\r\n", $skipped));
        }

        try {
            $results = $this->vvf_backbone_import_service->saveTransferedOrders();
        } catch (PDOException $e) {
            report($e);
            $this->logInfo(
                'datalink vvf backbone import：',
                [config('constant.data_link.global.results.fail')]
            );

            exit;
        }

        $this->logInfo('datalink vvf backbone import：data registration.', $results);

        $messages = $this->vvf_backbone_import_service->matching();
        $this->logInfo('datalink vvf backbone import：matching.', $messages);

        $this->logInfo('datalink vvf backbone import：end.', [config('constant.data_link.global.results.success')]);
    }

    /**
     * 自動連携 INFOログ出力
     *
     * @param  string $message
     * @param  array $contents
     * @return void
     */
    private function logInfo(string $message, array $contents = []): void
    {
        $this->logger->info(
            $message,
            array_merge([config('settings.data_link.order.orderings.program_name')], $contents)
        );
    }
}
