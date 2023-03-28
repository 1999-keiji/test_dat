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
use App\Models\Master\EndUser;
use App\Models\Master\EndUserFactory;
use App\Repositories\Master\CustomerRepository;
use App\Repositories\Master\EndUserRepository;
use App\Repositories\Master\EndUserFactoryRepository;

class EndUserService
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
     * @var \App\Services\Master\FactoryService;
     */
    private $factory_service;

    /**
     * @var \App\Repositories\Master\EndUserRepository
     */
    private $end_user_repo;

    /**
     * @var \App\Repositories\Master\EndUserFactoryRepository
     */
    private $end_user_factory_repo;

    /**
     * @var \App\Repositories\Master\CustomerRepository
     */
    private $customer_repo;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param \App\Extension\Logger\ApplicationLogger $application_logger
     * @param \App\Services\Master\FactoryService $factory_service
     * @param \App\Repositories\Master\EndUserRepository $end_user_repositry
     * @param \App\Repositories\Master\EndUserFactpryRepository $end_user_factory_repo
     */
    public function __construct(
        Connection $db,
        ApplicationLogger $application_logger,
        FactoryService $factory_service,
        EndUserRepository $end_user_repository,
        EndUserFactoryRepository $end_user_factory_repo,
        CustomerRepository $customer_repo
    ) {
        $this->db = $db;
        $this->application_logger = $application_logger;
        $this->factory_service = $factory_service;
        $this->end_user_repo = $end_user_repository;
        $this->end_user_factory_repo = $end_user_factory_repo;
        $this->customer_repo = $customer_repo;
    }

    /**
     * エンドユーザを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @throws
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchEndUsers(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'customer_code' => $params['customer_code'] ?? null,
            'customer_name' => $params['customer_name'] ?? null,
            'end_user_code' => $params['end_user_code'] ?? null,
            'end_user_name' => $params['end_user_name'] ?? null,
            'past_flag'     => $params['past_flag'] ?? null
        ];

        $end_users = $this->end_user_repo->search($params);
        if ($page > $end_users->lastPage() && $end_users->lastPage() !== 0) {
            throw new PageOverException('target page not exists.');
        }

        return $end_users;
    }

    /**
     * エンドユーザマスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\EndUser
     */
    public function createEndUser(array $params): EndUser
    {
        return $this->db->transaction(function () use ($params) {
            $end_user = $this->end_user_repo->create($params);

            if ($this->end_user_repo->getendUsersByEndUserCode($end_user->end_user_code)->count() === 1) {
                $this->end_user_factory_repo->linkFactories($end_user, $this->factory_service->getAllFactories());
            }

            return $end_user;
        });
    }

    /**
     * エンドユーザ工場マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\EndUserFactory
     */
    public function createEndUserFactory(array $params): EndUserFactory
    {
        return $this->end_user_factory_repo->create($params);
    }

    /**
     * エンドユーザマスタの更新
     *
     * @param \App\Models\Master\EndUser $end_user
     * @param array $params
     */
    public function updateEndUser(EndUser $end_user, array $params)
    {
        if (! $end_user->creating_type->isUpdatableCreatingType()) {
            $params = array_except($params, $end_user->getLinkedColumns());
        }

        $this->end_user_repo->update($end_user, $params);
    }

    /**
     * エンドユーザマスタの削除
     *
     * @param  \App\Models\Master\EndUser $end_user
     * @return void
     */
    public function deleteEndUser(EndUser $end_user): void
    {
        $this->db->transaction(function () use ($end_user) {
            $end_user->delete();

            $end_users = $this->end_user_repo->getendUsersByEndUserCode($end_user->end_user_code);
            if ($end_users->isEmpty()) {
                $end_user->end_user_factories->each(function ($euf) {
                    $this->deleteEndUserFactory($euf);
                });
            }
        });
    }

    /**
     * エンドユーザ工場マスタの削除
     *
     * @param  \App\Models\Master\EndUserFactory $end_user_factory
     * @return void
     */
    public function deleteEndUserFactory(EndUserFactory $end_user_factory): void
    {
        $end_user_factory->delete();
    }

    /**
     * エンドユーザマスタ自動連携
     *
     * @param void
     * @return bool
     */
    public function datalinkEndUser()
    {
        $records = [];
        $validation_error_messages = [];
        $end_file_path = config('settings.data_link.master.end_users.end_file_path');
        $tsv_file_path = config('settings.data_link.master.end_users.tsv_file_path');

        /** 「エンドファイル」の有無確認 **/
        if (!(\File::exists($end_file_path))) {
            \Log::error(
                config('settings.data_link.master.end_users.program_name').
                config('constant.data_link.global.error_list.end_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $end_file_path]
            );
            return false;
        }
        $this->logInfo('datalink end user：end file existence check.');

        /** 「エンドユーザマスタ」TSVファイル取り込み **/
        if (!(\File::exists($tsv_file_path))) {
            \Log::error(
                config('settings.data_link.master.end_users.program_name').
                config('constant.data_link.global.error_list.tsv_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $tsv_file_path]
            );
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.end_users.program_name')));
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
                if (count($tsv_row) < config('settings.data_link.master.end_users.tsv_file_columns')) {
                    throw new DataLinkException(
                        config('settings.data_link.master.end_users.program_name').
                        config('constant.data_link.global.error_list.failed_to_import_tsv_file').
                        sprintf(
                            config('constant.data_link.global.error_message_not_enough'),
                            $row_index,
                            count($tsv_row),
                            config('settings.data_link.master.end_users.tsv_file_columns')
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
                ->send(new DataLinkErrorMail(config('settings.data_link.master.end_users.program_name')));
            return false;
        } finally {
            unset($tsv_file);
        }
        if (!empty($validation_error_messages)) {
            \Log::error(
                config('settings.data_link.master.end_users.program_name').
                config('constant.data_link.global.error_list.validation_error').
                ' at '.__FILE__.':'.__line__,
                $validation_error_messages
            );
            $error_mail = new DataLinkErrorMail(
                config('settings.data_link.master.end_users.program_name'),
                config('constant.data_link.global.error_message_validation')
            );
            $error_mail->setValidationMessage($validation_error_messages);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send($error_mail);
            return false;
        }
        $this->logInfo('datalink end user：tsv file import.');

        /** 取り込んだデータの登録 **/
        try {
            $regist_count = $this->recordRegistration($records);
            $this->logInfo('datalink end user：data registration.', $regist_count);
        } catch (DataLinkException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.end_users.program_name'))
            );
            return false;
        } catch (PDOException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.end_users.program_name'))
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
        $set_columns  = config('settings.data_link.master.end_users.set_column_index');
        $set_fix_info = config('settings.data_link.master.end_users.set_fix_info');
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
     * エンドユーザマスタ,エンドユーザ工場マスタ登録処理
     * @param $records
     */
    private function recordRegistration($records)
    {
        $create_count = 0;
        $update_count = 0;
        $this->db->transaction(function () use ($records, &$create_count, &$update_count) {
            $default_customer = $this->customer_repo->searchDefaultCustomer();
            if ($default_customer == null) {
                throw new DataLinkException(
                    config('settings.data_link.master.end_users.program_name').
                    config('constant.data_link.global.error_list.failed_to_save_data').
                    config('constant.data_link.master.end_user.error_message_not_exist')
                );
            }
            foreach ($records as $params) {
                $end_user = $this->end_user_repo->searchPrimary(
                    $params['end_user_code'],
                    $params['application_started_on']
                );
                if ($end_user == null) {
                    $params['customer_code'] = $default_customer->customer_code;
                    $end_user = $this->end_user_repo->create($params);
                    $factories = $this->factory_service->getAll()->getNotLinkedEndUserFactories($end_user);
                    if (count($factories) > 0) {
                        $this->end_user_factory_repo->linkFactories($end_user, $factories);
                    }
                    $create_count++;
                } else {
                    unset($params['can_display']);
                    $this->end_user_repo->update($end_user, $params);
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
            config('settings.data_link.master.end_users.zip_file_path')
            .config('settings.data_link.master.end_users.zip_file_name'),
            Chronos::now()->format('YmdHis')
        );
        if ($zip->open($zip_file_name, \ZipArchive::CREATE)) {
            $zip->addFile($tsv_file_path);
            $zip->close();
        }
        \File::delete($end_file_path, $tsv_file_path);
        $files = \File::allFiles(config('settings.data_link.master.end_users.zip_file_path'));
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
            array_merge([config('settings.data_link.master.end_users.program_name')], $contents)
        );
    }

    /**
     * API用にエンドユーザマスタを検索
     *
     * @param  array $params
     * @return array
     */
    public function searchEndUsersForSearchingApi(array $params): array
    {
        return $this->end_user_repo
            ->searchForSearchingApi([
                'end_user_code' => $params['master_code'] ?? null,
                'end_user_name' => $params['master_name'] ?? null,
                'end_user_name2' => $params['master_name2'] ?? null,
                'end_user_abbreviation' => $params['master_abbreviation'] ?? null,
                'end_user_name_kana' => $params['master_name_kana'] ?? null,
                'address' => $params['master_address'] ?? null,
                'phone_number' => $params['master_phone_number'] ?? null,
                'factory_code' => $params['factory_code'] ?? null,
                'customer_code' => $params['customer_code'] ?? null
            ])
            ->toResponseForSearchingApi();
    }
}
