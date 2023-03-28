<?php

declare(strict_types=1);

namespace App\Services\Master;

use PDOException;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Mail;
use Cake\Chronos\Chronos;
use App\Exceptions\DataLinkException;
use App\Extension\Logger\ApplicationLogger;
use App\Mail\Master\DataLinkErrorMail;
use App\Repositories\Master\DeliveryFactoryProductRepository;
use App\Repositories\Master\FactoryRepository;
use App\Repositories\Master\FactoryProductRepository;
use App\Repositories\Master\FactoryProductPriceRepository;
use App\Repositories\Master\FactoryProductSpecialPriceRepository;
use App\Repositories\Master\ProductPriceRepository;
use App\Repositories\Master\ProductSpecialPriceRepository;

class ProductPriceService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactoryRepository
     */
    private $factory_repo;

    /**
     * @var \App\Repositories\Master\ProductPriceRepository
     */
    private $product_price_repo;

    /**
     * @var \App\Repositories\Master\ProductSpecialPriceRepository
     */
    private $product_special_price_repo;

    /**
     * @var \App\Repositories\Master\FactoryProductRepository
     */
    private $factory_product_repo;

    /**
     * @var \App\Repositories\Master\FactoryProductPriceRepository
     */
    private $factory_product_price_repo;

    /**
     * @var \App\Repositories\Master\DeliveryFactoryProductRepository
     */
    private $delivery_factory_product_repo;

    /**
     * @var \App\Repositories\Master\FactoryProductSpecialPriceRepository
     */
    private $factory_product_special_price_repo;

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Extension\Logger\ApplicationLogger $application_logger
     * @param  \App\Repositories\Master\FactoryRepository $factory_repositry
     * @param  \App\Repositories\Master\ProductPriceRepository $product_price_repositry
     * @param  \App\Repositories\Master\ProductSpecialPriceRepository $product_special_price_repositry
     * @param  \App\Repositories\Master\FactoryProductRepository $factory_product_repository
     * @param  \App\Repositories\Master\FactoryProductPriceRepository $factory_product_price_repository
     * @param  \App\Repositories\Master\DeliveryFactoryProductRepository $delivery_factory_product_repository
     * @param  \App\Repositories\Master\FactoryProductSpecialPriceRepository $factory_product_special_price_repository
     */
    public function __construct(
        Connection $db,
        ApplicationLogger $application_logger,
        FactoryRepository $factory_repositry,
        ProductPriceRepository $product_price_repositry,
        ProductSpecialPriceRepository $product_special_price_repositry,
        FactoryProductRepository $factory_product_repository,
        FactoryProductPriceRepository $factory_product_price_repository,
        DeliveryFactoryProductRepository $delivery_factory_product_repository,
        FactoryProductSpecialPriceRepository $factory_product_special_price_repository
    ) {
        $this->db = $db;
        $this->application_logger = $application_logger;
        $this->factory_repo = $factory_repositry;
        $this->product_price_repo = $product_price_repositry;
        $this->product_special_price_repo = $product_special_price_repositry;
        $this->factory_product_repo = $factory_product_repository;
        $this->factory_product_price_repo = $factory_product_price_repository;
        $this->delivery_factory_product_repo = $delivery_factory_product_repository;
        $this->factory_product_special_price_repo = $factory_product_special_price_repository;
    }

    /**
     * 商品価格マスタ自動連携
     *
     * @param void
     * @return bool
     */
    public function datalinkProductPrice()
    {
        $records = [];
        $validation_error_messages = [];
        $end_file_path = config('settings.data_link.master.product_prices.end_file_path');
        $tsv_file_path = config('settings.data_link.master.product_prices.tsv_file_path');

        // エンドファイル存在チェック
        if (!(\File::exists($end_file_path))) {
            \Log::error(
                config('settings.data_link.master.product_prices.program_name').
                config('constant.data_link.global.error_list.end_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $end_file_path]
            );
            return false;
        }
        $this->logInfo('datalink product price：end file existence check.');

        // TSVファイル存在チェック
        if (!(\File::exists($tsv_file_path))) {
            \Log::error(
                config('settings.data_link.master.product_prices.program_name').
                config('constant.data_link.global.error_list.tsv_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $tsv_file_path]
            );
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.product_prices.program_name')));
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
                if (count($tsv_row) < config('settings.data_link.master.product_prices.tsv_file_columns')) {
                    throw new DataLinkException(
                        config('settings.data_link.master.product_prices.program_name').
                        config('constant.data_link.global.error_list.failed_to_import_tsv_file').
                        sprintf(
                            config('constant.data_link.global.error_message_not_enough'),
                            $row_index,
                            count($tsv_row),
                            config('settings.data_link.master.product_prices.tsv_file_columns')
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
                ->send(new DataLinkErrorMail(config('settings.data_link.master.product_prices.program_name')));
            return false;
        } finally {
            unset($tsv_file);
        }
        // バリデーションエラーあり
        if (!empty(array_filter($validation_error_messages))) {
            \Log::error(
                config('settings.data_link.master.product_prices.program_name').
                config('constant.data_link.global.error_list.validation_error').
                ' at '.__FILE__.':'.__line__,
                $validation_error_messages
            );
            $error_mail = new DataLinkErrorMail(
                config('settings.data_link.master.product_prices.program_name'),
                config('constant.data_link.global.error_message_validation')
            );
            $error_mail->setValidationMessage($validation_error_messages);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send($error_mail);
            return false;
        }
        $this->logInfo('datalink product price：tsv file import.');

        // 取り込みデータの登録
        try {
            $regist_count = $this->recordRegistration($records);
            $this->logInfo('datalink product price：data registration.', $regist_count);
        } catch (DataLinkException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.product_prices.program_name'))
            );
            return false;
        } catch (PDOException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.product_prices.program_name'))
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
        $set_columns = config('settings.data_link.master.product_prices.set_column_index');

        foreach ($set_columns as $key => $value) {
            if ((strstr($value['validate'], 'nullable') === false)
            || (strstr($value['validate'], 'string') !== false)
            || ($tsv_row[$value['index']] !== '')) {
                $return_row[$key]       = $tsv_row[$value['index']];
                $validation_rules[$key] = $value['validate'];
            }
        }

        // バリデーション処理
        $validator = \Validator::make($return_row, $validation_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }
        return $return_row;
    }

    /**
     * 自動連携 商品価格マスタ or 商品特価マスタ登録処理
     * @param $records
     */
    private function recordRegistration($records)
    {
        $create_count = 0;
        $update_count = 0;
        $this->db->transaction(function () use ($records, &$create_count, &$update_count) {
            foreach ($records as $record) {
                // 工場コードの取得
                $factory = $this->factory_repo->getFactoryBySupplierCode($record['supplier_code']);
                if ($factory === null) {
                    throw new DataLinkException(
                        config('settings.data_link.master.product_prices.program_name').
                        config('constant.data_link.global.error_list.failed_to_save_data').
                        sprintf(
                            config('constant.data_link.master.product_prices.error_message_not_exist'),
                            $record['supplier_code']
                        )
                    );
                }
                $record['factory_code'] = $factory->factory_code;

                // 仕入先価格マスタ区分による分岐
                $special_price_class
                = config('settings.data_link.master.product_prices.supplier_price_class_list.product_special_prices');
                if ($record['supplier_price_class'] === $special_price_class) {
                    $record = $this->recordReconfiguration($record);
                    // 商品特価マスタ登録処理
                    $product_special_price = $this->product_special_price_repo->getProductSpecialPrice($record);
                    if ($product_special_price === null) {
                        $this->product_special_price_repo->create($record);
                        $create_count++;
                    } else {
                        $this->product_special_price_repo->update($product_special_price, $record);
                        $update_count++;
                    }

                    $this->delivery_factory_product_repo
                        ->getDeliveryFactoryProductsByDeliveryDestinationAndProduct(
                            $record['delivery_destination_code'],
                            $record['factory_code'],
                            $record['product_code']
                        )
                        ->each(function ($dfp) use ($record) {
                            $factory_product_special_price = $dfp->factory_product_special_prices
                                ->filterByCurrencyAndApplicationDate(
                                    $record['currency_code'],
                                    $record['application_started_on']
                                );

                            if (is_null($factory_product_special_price)) {
                                $this->factory_product_special_price_repo->create($dfp, [
                                    'currency_code' => $record['currency_code'],
                                    'application_started_on' => $record['application_started_on'],
                                    'application_ended_on' => $record['application_ended_on'],
                                    'unit_price' => $record['unit_price']
                                ]);
                            }
                        });
                } else {
                    $record = $this->recordReconfiguration($record);
                    // 商品価格マスタ登録処理
                    $product_price = $this->product_price_repo->getProductPrice($record);
                    if ($product_price === null) {
                        $this->product_price_repo->create($record);
                        $create_count++;
                    } else {
                        $this->product_price_repo->update($product_price, $record);
                        $update_count++;
                    }

                    $this->factory_product_repo
                        ->getFactoryProductsByFactoryAndProduct($record['factory_code'], $record['product_code'])
                        ->each(function ($fp) use ($record) {
                            $factory_product_price = $fp->factory_product_prices
                                ->filterByCurrencyAndApplicationDate(
                                    $record['currency_code'],
                                    $record['application_started_on']
                                );

                            if (is_null($factory_product_price)) {
                                $this->factory_product_price_repo->create([
                                    'factory_code' => $fp->factory_code,
                                    'factory_product_sequence_number' => $fp->sequence_number,
                                    'currency_code' => $record['currency_code'],
                                    'application_started_on' => $record['application_started_on'],
                                    'unit_price' => $record['unit_price']
                                ]);
                            }
                        });
                }
            }
        });

        return ['create' => $create_count, 'update' => $update_count];
    }

    /**
     * 自動連携 レコード再設定
     * @param $record
     */
    private function recordReconfiguration($record)
    {
        $supplier_price_class_list = config('settings.data_link.master.product_prices.supplier_price_class_list');
        foreach ($supplier_price_class_list as $class_name => $num) {
            if ($record['supplier_price_class'] === $num) {
                $unset_list = config('settings.data_link.master.product_prices.unset_list')[$class_name];
            }
        }
        foreach ($unset_list as $value) {
            unset($record[$value]);
        }
        return $record;
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
            config('settings.data_link.master.product_prices.zip_file_path').
            config('settings.data_link.master.product_prices.zip_file_name'),
            Chronos::now()->format('YmdHis')
        );
        if ($zip->open($zip_file_name, \ZipArchive::CREATE)) {
            $tsv_file_name = basename($tsv_file_path);
            $zip->addFile($tsv_file_path, $tsv_file_name);
            $zip->close();
        }
        \File::delete($end_file_path, $tsv_file_path);
        $files = \File::allFiles(config('settings.data_link.master.product_prices.zip_file_path'));
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
            array_merge([config('settings.data_link.master.product_prices.program_name')], $contents)
        );
    }
}
