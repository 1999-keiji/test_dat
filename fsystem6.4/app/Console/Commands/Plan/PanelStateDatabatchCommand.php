<?php

namespace App\Console\Commands\Plan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use App\Extension\Logger\ApplicationLogger;
use App\Services\Plan\PanelStateService;

class PanelStateDatabatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature
        = 'command:databatch-panel_state'
        .' {--s|show-panels : パネル指定情報を表示}'
        .' {--d|date= : 日付}'
        .' {--f|factory_code= : 工場コード}'
        .' {--r|bed_row= : ベッド段}'
        .' {--c|bed_column= : ベッド列}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'パネル状態データバッチ';

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $logger;

    /**
     * @var \App\Services\Master\PanelStateService
     */
    private $panel_state_service;

    /**
     * Create a new command instance.
     *
     * @param  \App\Extension\Logger\ApplicationLogger $logger
     *
     * @return void
     */
    public function __construct(ApplicationLogger $logger, PanelStateService $panel_state_service)
    {
        parent::__construct();

        $this->logger = $logger;
        $this->panel_state_service = $panel_state_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logInfo('batch process：start.');

        $show_panels = $this->option('show-panels');
        $options = [
            'factory_code' => $this->option('factory_code'),
            'bed_row' => $this->option('bed_row'),
            'bed_column' => $this->option('bed_column'),
            'date' => $this->option('date')
        ];

        $errors = $this->checkCommandOptions($options);
        if (count($errors) !== 0) {
            foreach ($errors as $message) {
                $this->error($message);
            }

            $this->logInfo('batch process：unexpected end.', $errors);
            return;
        }

        $factory_beds = $this->panel_state_service->replicatePanelStates($options, $show_panels);
        if ($show_panels) {
            $this->table(['factory_code', 'bed_row', 'bed_column', 'date'], $factory_beds->toArray());
        }

        $this->logInfo('batch process：end.');
    }

    /**
     * パラメータエラーチェック
     *
     * @param  array $options
     * @return array $errors
     */
    private function checkCommandOptions(array $options): ?array
    {
        $errors = [];
        $validator = Validator::make($options, [
            'factory_code' => ['nullable', 'exists:factories'],
            'bed_row' => ['nullable', 'integer', 'min:1', 'max:99'],
            'bed_column' => ['nullable', 'integer', 'min:1', 'max:99'],
            'date' => ['nullable', 'date_format:Y-m-d']
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }

        return $errors;
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
        $this->logger->info($message, $contents);
    }
}
