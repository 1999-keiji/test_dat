<?php

declare(strict_types=1);

namespace App\Services\Master;

use PDOException;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Cake\Chronos\Chronos;
use App\Exceptions\DataLinkException;
use App\Exceptions\PageOverException;
use App\Extension\Logger\ApplicationLogger;
use App\Mail\Master\DataLinkErrorMail;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Collections\DeliveryDestinationCollection;
use App\Repositories\Master\EndUserRepository;
use App\Repositories\Master\DeliveryDestinationRepository;
use App\Repositories\Master\DeliveryWarehouseRepository;

class DeliveryDestinationService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @var \App\Services\Master\WarehouseService
     */
    private $warehouse_service;

    /**
     * @var \App\Repositories\Master\DeliveryDestinationRepository
     */
    private $delivery_destination_repo;

    /**
     * @var \App\Repositories\Master\DeliveryWarehouseRepository
     */
    private $delivery_warehouse_repo;

    /**
     * @var \App\Repositories\Master\EndUserRepository
     */
    private $end_user_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Extension\Logger\ApplicationLogger $application_logger
     * @param  \App\Services\Master\WarehouseService $warehouse_service
     * @param  \App\Repositories\Master\DeliveryDestinationRepository $delivery_destination_repo
     * @param  \App\Repositories\Master\DeliveryWarehouseRepository $delivery_warehouse_repo
     * @param  \App\Repositories\Master\EndUserRepository $end_user_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        ApplicationLogger $application_logger,
        WarehouseService $warehouse_service,
        DeliveryDestinationRepository $delivery_destination_repo,
        DeliveryWarehouseRepository $delivery_warehouse_repo,
        EndUserRepository $end_user_repo
    ) {
        $this->db = $db;
        $this->application_logger = $application_logger;
        $this->warehouse_service = $warehouse_service;

        $this->delivery_destination_repo = $delivery_destination_repo;
        $this->delivery_warehouse_repo = $delivery_warehouse_repo;
        $this->end_user_repo = $end_user_repo;
    }

    /**
     * 納入先マスタの取得
     *
     * @param  string $delivery_destination_code
     * @return \App\Models\Master\DeliveryDestination $delivery_destination
     */
    public function find(string $delivery_destination_code): DeliveryDestination
    {
        return $this->delivery_destination_repo->find($delivery_destination_code);
    }

    /**
     * 納入先マスタを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws
     */
    public function searchDeliveryDestinations(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'delivery_destination_code' => $params['delivery_destination_code'] ?? null,
            'delivery_destination_name' => $params['delivery_destination_name'] ?? null
        ];

        $delivery_destinations = $this->delivery_destination_repo->search($params);
        if ($page > $delivery_destinations->lastPage() && $delivery_destinations->lastPage() !== 0) {
            throw new PageOverException('target page not exists.');
        }

        return $delivery_destinations;
    }

    /**
     * API用に納入先マスタを検索
     *
     * @param  array $params
     * @return array
     */
    public function searchDeliveryDestinationsForSearchingApi(array $params): array
    {
        $delivery_destinations = $this->delivery_destination_repo
            ->searchForSearchingApi([
                'delivery_destination_code' => $params['master_code'] ?? null,
                'delivery_destination_name' => $params['master_name'] ?? null,
                'delivery_destination_name2' => $params['master_name2'] ?? null,
                'delivery_destination_abbreviation' => $params['master_abbreviation'] ?? null,
                'delivery_destination_name_kana' => $params['master_name_kana'] ?? null,
                'address' => $params['master_address'] ?? null,
                'phone_number' => $params['phone_number'] ?? null,
                'factory_code' => $params['factory_code'] ?? null,
                'limited' => array_key_exists('limited', $params) ? (bool)$params['limited'] : true
            ]);

        $end_users = $this->end_user_repo
            ->getCurrentApplicatedEndUsers($delivery_destinations->pluck('end_user_code')->unique()->all());

        return $delivery_destinations
            ->map(function ($dd) use ($end_users) {
                $dd->end_user = $end_users->findByEndUserCode($dd->end_user_code);
                return $dd;
            })
            ->reject(function ($dd) {
                return is_null($dd->end_user);
            })
            ->toResponseForSearchingApi();
    }

    /**
     * 納入先マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryDestination $delivery_destination
     */
    public function createDeliveryDestination(array $params): DeliveryDestination
    {
        return $this->db->transaction(function () use ($params) {
            $delivery_destination = $this->delivery_destination_repo->create($params);
            $this->delivery_warehouse_repo->linkWarehouses(
                $delivery_destination,
                $this->warehouse_service->getDefaultWarehouses()
            );

            return $delivery_destination;
        });
    }

    /**
     * 納入先マスタの更新
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  array $params
     * @return \App\Models\Master\DeliveryDestination $delivery_destination
     */
    public function updateDeliveryDestination(
        DeliveryDestination $delivery_destination,
        array $params
    ): DeliveryDestination {
        if (! $delivery_destination->creating_type->isUpdatableCreatingType()) {
            $params = array_except($params, $delivery_destination->getLinkedColumns());
        }

        if (array_key_exists('prefecture_code', $params) && is_null($params['prefecture_code'])) {
            $params['prefecture_code'] = '';
        }

        return $this->delivery_destination_repo->update($delivery_destination, $params);
    }

    /**
     * 納入先マスタの削除
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return void
     */
    public function deleteDeliveryDestination(DeliveryDestination $delivery_destination): void
    {
        $delivery_destination->delete();
    }

    /**
     * 納入先マスタ自動連携
     *
     * @param void
     * @return bool
     */
    public function datalinkDeliveryDestination()
    {
        $records = [];
        $validation_error_messages = [];
        $end_file_path = config('settings.data_link.master.delivery_destinations.end_file_path');
        $tsv_file_path = config('settings.data_link.master.delivery_destinations.tsv_file_path');
        /** 「エンドファイル」の有無確認 **/
        if (!(\File::exists($end_file_path))) {
            \Log::error(
                config('settings.data_link.master.delivery_destinations.program_name')
                .config('constant.data_link.global.error_list.end_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'end file path' => $end_file_path]
            );
            return false;
        }
        $this->logInfo('datalink delivery destination：end file existence check.');
        /** 「納入先マスタ」TSVファイル取り込み **/
        if (!(\File::exists($tsv_file_path))) {
            \Log::error(
                config('settings.data_link.master.delivery_destinations.program_name')
                .config('constant.data_link.global.error_list.tsv_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'tsv file path' => $tsv_file_path]
            );
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.delivery_destinations.program_name')));
            return false;
        }
        try {
            $tsv_file = new \SplFileObject($tsv_file_path, 'r');
            $tsv_file->setFlags(\SplFileObject::READ_CSV |\SplFileObject::READ_AHEAD);
            $tsv_file->setCsvControl("\t");
            $row_index = 0;
            foreach ($tsv_file as $tsv_row) {
                $row_index++;
                if (count($tsv_row) == 1 && empty($tsv_row[0])) {
                    continue;
                }
                if (count($tsv_row) < config('settings.data_link.master.delivery_destinations.tsv_file_columns')) {
                    throw new DataLinkException(
                        config('settings.data_link.master.delivery_destinations.program_name').
                        config('constant.data_link.global.error_list.failed_to_import_tsv_file').
                        sprintf(
                            config('constant.data_link.global.error_message_not_enough'),
                            $row_index,
                            count($tsv_row),
                            config('settings.data_link.master.delivery_destinations.tsv_file_columns')
                        )
                    );
                }
                $validation_errors = [];
                $records[] = $this->setColumns($tsv_row, $validation_errors);
                if (!empty($validation_errors)) {
                    $validation_error_messages[$row_index] = $validation_errors;
                }
            }
        } catch (DataLinkException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.delivery_destinations.program_name')));
            return false;
        } finally {
            unset($tsv_file);
        }
        if (!empty($validation_error_messages)) {
            \Log::error(
                config('settings.data_link.master.delivery_destinations.program_name').
                config('constant.data_link.global.error_list.validation_error').
                ' at '.__FILE__.':'.__line__,
                $validation_error_messages
            );
            $error_mail = new DataLinkErrorMail(
                config('settings.data_link.master.delivery_destinations.program_name'),
                config('constant.data_link.global.error_message_validation')
            );
            $error_mail->setValidationMessage($validation_error_messages);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send($error_mail);
            return false;
        }
        $this->logInfo('datalink delivery destination：tsv file import.');

        /** 取り込んだデータの登録 **/
        try {
            $regist_count = $this->recordRegistration($records);
            $this->logInfo('datalink delivery destination：data registration.', $regist_count);
        } catch (PDOException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.delivery_destinations.program_name'))
            );
            return false;
        }

        /** 後処理 **/
        $this->closingProcess($end_file_path, $tsv_file_path);
        return true;
    }

    /**
     * 取得したTSVファイルの中身とカラム名を紐付ける
     * @param $tsv_row
     * @param $errors
     * @return array
     */
    private function setColumns($tsv_row, &$errors): array
    {
        $return_row       = [];
        $validation_rules = [];
        $set_columns  = config('settings.data_link.master.delivery_destinations.set_column_index');
        $set_fix_info = config('settings.data_link.master.delivery_destinations.set_fix_info');
        foreach ($set_columns as $key => $value) {
            if ((strstr($value['validate'], 'nullable') === false)
            || (strstr($value['validate'], 'string') !== false)
            || ($tsv_row[$value['index']] !== '')) {
                $return_row[$key]       = $tsv_row[$value['index']];
                $validation_rules[$key] = $value['validate'];
            }
        }
        foreach ($set_fix_info as $key => $value) {
            $return_row[$key] = $value;
        }
        $validator = \Validator::make($return_row, $validation_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }
        return $return_row;
    }

    /**
     * 納入先マスタ登録処理
     * @param $records
     */
    private function recordRegistration($records)
    {
        $create_count = 0;
        $update_count = 0;
        $this->db->transaction(function () use ($records, &$create_count, &$update_count) {
            $default_factory_warehouse = $this->warehouse_service->getDefaultWarehouses();
            foreach ($records as $params) {
                $delivery_destination
                    = $this->delivery_destination_repo->searchPrimary($params['delivery_destination_code']);
                if ($delivery_destination == null) {
                    $delivery_destination = $this->delivery_destination_repo->create($params);
                    $this->delivery_warehouse_repo->linkWarehouses(
                        $delivery_destination,
                        $this->warehouse_service->getDefaultWarehouses()
                    );
                    $create_count++;
                } else {
                    unset($params['can_display']);
                    $this->delivery_destination_repo->update($delivery_destination, $params);
                    $update_count++;
                }
            }
        });
        return ['create' => $create_count, 'update' => $update_count];
    }

    /**
     * 自動連携 後処理
     * @param $end_file_path
     * @param $tsv_file_path
     */
    private function closingProcess($end_file_path, $tsv_file_path)
    {
        $zip = new \ZipArchive();
        $zip_file_name = sprintf(
            config('settings.data_link.master.delivery_destinations.zip_file_path')
            .config('settings.data_link.master.delivery_destinations.zip_file_name'),
            Chronos::now()->format('YmdHis')
        );
        if ($zip->open($zip_file_name, \ZipArchive::CREATE)) {
            $zip->addFile($tsv_file_path);
            $zip->close();
        }
        \File::delete($end_file_path, $tsv_file_path);
        $files = \File::allFiles(config('settings.data_link.master.delivery_destinations.zip_file_path'));
        foreach ($files as $file) {
            $date_format = 'Y/m/d H:i:s';
            $last_modified = Chronos::createFromFormat($date_format, date($date_format, \File::lastModified($file)));
            if (!$last_modified->wasWithinLast(config('settings.data_link.global.storage_period'))) {
                \File::delete($file);
            }
        }
        return;
    }

    /**
     * INFOログ出力
     */
    public function logInfo($message, $contents = [])
    {
        $this->application_logger->info(
            $message,
            array_merge([config('settings.data_link.master.delivery_destinations.program_name')], $contents)
        );
    }
}
