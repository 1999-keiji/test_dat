<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Mail;
use PDOException;
use App\Exceptions\DataLinkException;
use App\Repositories\Master\SupplierRepository;
use App\Mail\Master\DataLinkErrorMail;
use App\Extension\Logger\ApplicationLogger;
use Cake\Chronos\Chronos;

class SupplierService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\SupplierRepository
     */
    private $supplier_repo;

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param \App\Extension\Logger\ApplicationLogger $application_logger
     * @param \App\Repositories\Master\SupplierRepository $supplier_repositry
     */
    public function __construct(
        Connection $db,
        ApplicationLogger $application_logger,
        SupplierRepository $supplier_repositry
    ) {
        $this->db = $db;
        $this->application_logger = $application_logger;
        $this->supplier_repo = $supplier_repositry;
    }

    /**
     * 仕入先マスタ自動連携
     *
     * @param void
     * @return bool
     */
    public function datalinkSupplier()
    {
        $records = [];
        $validation_error_messages = [];
        $end_file_path = config('settings.data_link.master.suppliers.end_file_path');
        $tsv_file_path = config('settings.data_link.master.suppliers.tsv_file_path');

        // エンドファイル存在チェック
        if (!(\File::exists($end_file_path))) {
            \Log::error(
                config('settings.data_link.master.suppliers.program_name').
                config('constant.data_link.global.error_list.end_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $end_file_path]
            );
            return false;
        }
        $this->logInfo('datalink supplier：end file existence check.');

        // TSVファイル存在チェック
        if (!(\File::exists($tsv_file_path))) {
            \Log::error(
                config('settings.data_link.master.suppliers.program_name').
                config('constant.data_link.global.error_list.tsv_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $tsv_file_path]
            );
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.suppliers.program_name')));
            return false;
        }

        // TSVファイル取り込み
        try {
            $tsv_file = new \SplFileObject($tsv_file_path, 'r');
            $tsv_file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_AHEAD);
            $tsv_file->setCsvControl("\t");
            $row_index = 0;

            foreach ($tsv_file as $tsv_row) {
                $row_index++;
                if (count($tsv_row) === 1 && empty($tsv_row[0])) {
                    continue;
                }
                if (count($tsv_row) < config('settings.data_link.master.suppliers.tsv_file_columns')) {
                    throw new DataLinkException(
                        config('settings.data_link.master.suppliers.program_name').
                        config('constant.data_link.global.error_list.failed_to_import_tsv_file').
                        sprintf(
                            config('constant.data_link.global.error_message_not_enough'),
                            $row_index,
                            count($tsv_row),
                            config('settings.data_link.master.suppliers.tsv_file_columns')
                        )
                    );
                }

                // データの整形＆バリデーション
                $validation_errors = [];
                $records[] = $this->setColumns($tsv_row, $validation_errors);
                if (!empty($validation_errors)) {
                    $validation_error_messages[$row_index] = $validation_errors;
                }
            }
        } catch (DataLinkException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.suppliers.program_name')));
            return false;
        } finally {
            unset($tsv_file);
        }
        // バリデーションエラーあり
        if (!empty(array_filter($validation_error_messages))) {
            \Log::error(
                config('settings.data_link.master.suppliers.program_name').
                config('constant.data_link.global.error_list.validation_error').
                ' at '.__FILE__.':'.__line__,
                $validation_error_messages
            );
            $error_mail = new DataLinkErrorMail(
                config('settings.data_link.master.suppliers.program_name'),
                config('constant.data_link.global.error_message_validation')
            );
            $error_mail->setValidationMessage($validation_error_messages);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send($error_mail);
            return false;
        }
        $this->logInfo('datalink supplier：tsv file import.');

        // 取り込みデータの登録
        try {
            $regist_count = $this->recordRegistration($records);
            $this->logInfo('datalink supplier：data registration.', $regist_count);
        } catch (PDOException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.suppliers.program_name'))
            );
            return false;
        }

        // 後処理
        $this->closingProcess($end_file_path, $tsv_file_path);
        return true;
    }

    /**
     * 自動連携 取得したTSVファイルの中身とカラム名を紐付け＆バリデーション処理
     * @param $tsv_row array
     * @param &$errors array
     * @return array
     */
    private function setColumns($tsv_row, &$errors): array
    {
        $return_row = [];
        $validation_rules = [];
        $set_columns = config('settings.data_link.master.suppliers.set_column_index');
        $set_fix_info = config('settings.data_link.master.suppliers.set_fix_info');

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

        //　バリデーション処理
        $validator = \Validator::make($return_row, $validation_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }
        return $return_row;
    }

    /**
     * 自動連携 仕入先マスタ登録処理
     * @param $records
     */
    private function recordRegistration($records)
    {
        $create_count = 0;
        $update_count = 0;
        $this->db->transaction(function () use ($records, &$create_count, &$update_count) {
            foreach ($records as $record) {
                $supplier = $this->supplier_repo->getSupplier($record);
                if ($supplier === null) {
                    $this->supplier_repo->create($record);
                    $create_count++;
                } else {
                    unset($record['can_display']);
                    $this->supplier_repo->update($supplier, $record);
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
            config('settings.data_link.master.suppliers.zip_file_path').
            config('settings.data_link.master.suppliers.zip_file_name'),
            Chronos::now()->format('YmdHis')
        );
        if ($zip->open($zip_file_name, \ZipArchive::CREATE)) {
            $tsv_file_name = basename($tsv_file_path);
            $zip->addFile($tsv_file_path, $tsv_file_name);
            $zip->close();
        }
        \File::delete($end_file_path, $tsv_file_path);
        $files = \File::allFiles(config('settings.data_link.master.suppliers.zip_file_path'));
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
     * 自動連携 INFOログ出力
     */
    public function logInfo($message, $contents = [])
    {
        $this->application_logger->info(
            $message,
            array_merge([config('settings.data_link.master.suppliers.program_name')], $contents)
        );
    }
}
