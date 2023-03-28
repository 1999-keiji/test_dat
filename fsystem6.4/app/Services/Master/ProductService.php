<?php

declare(strict_types=1);

namespace App\Services\Master;

use PDOException;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Cake\Chronos\Chronos;
use App\Exceptions\PageOverException;
use App\Exceptions\DataLinkException;
use App\Extension\Logger\ApplicationLogger;
use App\Mail\Master\DataLinkErrorMail;
use App\Models\Master\Product;
use App\Models\Master\Collections\ProductCollection;
use App\Repositories\Master\ProductRepository;
use App\Repositories\Master\ProductPriceRepository;
use App\Repositories\Master\ProductSpecialPriceRepository;
use App\Repositories\Master\SpeciesConverterRepository;

class ProductService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\ProductRepository
     */
    private $product_repo;

    /**
     * @var \App\Repositories\Master\ProductPriceRepository
     */
    private $product_price_repo;

    /**
     * @var \App\Repositories\Master\ProductSpecialPriceRepository
     */
    private $product_special_price_repo;

    /**
     * @var \App\Repositories\Master\SpeciesConverterRepository
     */
    private $species_converter_repo;

    /**
     * @var \App\Extension\Logger\ApplicationLogger
     */
    private $application_logger;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Extension\Logger\ApplicationLogger $application_logger
     * @param  \App\Repositories\Master\ProductRepository $product_repo
     * @param  \App\Repositories\Master\ProductPriceRepository $product_price_repo
     * @param  \App\Repositories\Master\ProductSpecialPriceRepository $product_special_price_repo
     * @param  \App\Repositories\Master\SpeciesConverterRepository $species_converter_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        ApplicationLogger $application_logger,
        ProductRepository $product_repo,
        ProductPriceRepository $product_price_repo,
        ProductSpecialPriceRepository $product_special_price_repo,
        SpeciesConverterRepository $species_converter_repo
    ) {
        $this->db = $db;
        $this->application_logger = $application_logger;
        $this->product_repo = $product_repo;
        $this->product_price_repo = $product_price_repo;
        $this->product_special_price_repo = $product_special_price_repo;
        $this->species_converter_repo = $species_converter_repo;
    }

    /**
     * すべての商品マスタを取得
     *
     * @return \App\Models\Master\Collections\ProductCollection
     */
    public function getAllProducts(): ProductCollection
    {
        return $this->product_repo->all();
    }

    /**
     * 商品マスタを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchProducts(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'species_code' => $params['species_code'] ?? null,
            'product_code' => $params['product_code'] ?? null,
            'product_name' => $params['product_name'] ?? null
        ];

        $products = $this->product_repo->search($params);
        if ($page > $products->lastPage() && $products->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $products;
    }

    /**
     * 指定された品種に紐づく商品を取得
     *
     * @param  array
     * @return array
     */
    public function getProductsForSearchingApi(array $params): array
    {
        $params = [
            'species_code' => $params['species_code'] ?? null
        ];

        return $this->product_repo->getProductsBySpecies($params)->toResponseForSearchingApi();
    }

    /**
     * 指定された工場、商品に紐づく商品価格を取得
     *
     * @param  array
     * @return array
     */
    public function getProductPricesForSearchingApi(array $params): array
    {
        $params = [
            'factory_code' => $params['factory_code'] ?? null,
            'product_code' => $params['product_code'] ?? null
        ];

        return $this->product_price_repo->getProductPrices($params)->toResponseForSearchingApi();
    }

    /**
     * 指定された納入先、工場、商品に紐づく商品特価を取得
     *
     * @param  array
     * @return array
     */
    public function getProductSpecialPricesForSearchingApi(array $params): array
    {
        $params = [
            'delivery_destination_code' => $params['delivery_destination_code'] ?? null,
            'factory_code' => $params['factory_code'] ?? null,
            'product_code' => $params['product_code'] ?? null
        ];

        return $this->product_special_price_repo->getProductSpecialPrices($params)->toResponseForSearchingApi();
    }

    /**
     * 商品マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Product
     */
    public function createProduct(array $params): Product
    {
        return $this->product_repo->create($params);
    }

    /**
     * 商品マスタの更新
     *
     * @param  \App\Models\Master\Product $product
     * @param  array $params
     * @return \App\Models\Master\Product $product
     */
    public function updateProduct(Product $product, array $params): Product
    {
        if (! $product->creating_type->isUpdatableCreatingType()) {
            $params = array_except($params, $product->getLinkedColumns());
        }

        return $this->product_repo->update($product, $params);
    }

    /**
     * 商品マスタの削除
     * 紐づく商品価格マスタ、商品特別価格マスタも削除
     *
     * @param  \App\Models\Master\Product $product
     * @return void
     */
    public function deleteProduct(Product $product): void
    {
        $this->db->transaction(function () use ($product) {
            $product->product_prices->each(function ($pp) {
                $pp->delete();
            });

            $product->product_special_prices->each(function ($psp) {
                $psp->delete();
            });

            $product->delete();
        });
    }

    /**
     * 商品マスタ自動連携
     *
     * @param void
     * @return bool
     */
    public function datalinkProduct()
    {
        $records = [];
        $validation_error_messages = [];
        $end_file_path = config('settings.data_link.master.products.end_file_path');
        $tsv_file_path = config('settings.data_link.master.products.tsv_file_path');

        // エンドファイル存在チェック
        if (!(\File::exists($end_file_path))) {
            \Log::error(
                config('settings.data_link.master.products.program_name').
                config('constant.data_link.global.error_list.end_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $end_file_path]
            );
            return false;
        }
        $this->logInfo('datalink product：end file existence check.');

        // TSVファイル存在チェック
        if (! (\File::exists($tsv_file_path))) {
            \Log::error(
                config('settings.data_link.master.products.program_name').
                config('constant.data_link.global.error_list.tsv_file_not_found'),
                ['at'=>__FILE__.':'.__line__, 'path' => $tsv_file_path]
            );
            Mail::to(config('settings.data_link.global.error_mail_to'))
                ->send(new DataLinkErrorMail(config('settings.data_link.master.products.program_name')));
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
                if (count($tsv_row) < config('settings.data_link.master.products.tsv_file_columns')) {
                    throw new DataLinkException(
                        config('settings.data_link.master.products.program_name').
                        config('constant.data_link.global.error_list.failed_to_import_tsv_file').
                        sprintf(
                            config('constant.data_link.global.error_message_not_enough'),
                            $row_index,
                            count($tsv_row),
                            config('settings.data_link.master.products.tsv_file_columns')
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
                ->send(new DataLinkErrorMail(config('settings.data_link.master.products.program_name')));
            return false;
        } finally {
            unset($tsv_file);
        }
        // バリデーションエラーあり
        if (!empty(array_filter($validation_error_messages))) {
            \Log::error(
                config('settings.data_link.master.products.program_name').
                config('constant.data_link.global.error_list.validation_error').
                ' at '.__FILE__.':'.__line__,
                $validation_error_messages
            );
            $error_mail = new DataLinkErrorMail(
                config('settings.data_link.master.products.program_name'),
                config('constant.data_link.global.error_message_validation')
            );
            $error_mail->setValidationMessage($validation_error_messages);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send($error_mail);
            return false;
        }
        $this->logInfo('datalink product：tsv file import.');

        // 取り込みデータの登録
        try {
            $regist_count = $this->recordRegistration($records);
            $this->logInfo('datalink product：data registration.', $regist_count);
        } catch (DataLinkException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.products.program_name'))
            );
            return false;
        } catch (PDOException $exception) {
            report($exception);
            Mail::to(config('settings.data_link.global.error_mail_to'))->send(
                new DataLinkErrorMail(config('settings.data_link.master.products.program_name'))
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
        $set_columns = config('settings.data_link.master.products.set_column_index');
        $set_fix_info = config('settings.data_link.master.products.set_fix_info');

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
     * 自動連携 商品マスタ登録処理
     * @param $records array
     */
    private function recordRegistration($records)
    {
        $create_count = 0;
        $update_count = 0;
        $this->db->transaction(function () use ($records, &$create_count, &$update_count) {
            foreach ($records as $record) {
                $product = $this->product_repo->getProduct($record['product_code']);
                if ($product === null) {
                    $species_converter = $this->species_converter_repo->getSpeciesConverter(
                        $record['product_large_category'],
                        $record['product_middle_category']
                    );
                    if ($species_converter === null) {
                        throw new DataLinkException(
                            config('settings.data_link.master.products.program_name').
                            config('constant.data_link.global.error_list.failed_to_save_data').
                            sprintf(
                                config('constant.data_link.master.product.error_message_not_exist'),
                                $record['product_large_category'],
                                $record['product_middle_category']
                            )
                        );
                    }
                    $record['species_code'] = $species_converter->species_code;
                    $this->product_repo->create($record);
                    $create_count++;
                } else {
                    $this->product_repo->update($product, $record);
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
            config('settings.data_link.master.products.zip_file_path').
            config('settings.data_link.master.products.zip_file_name'),
            Chronos::now()->format('YmdHis')
        );
        if ($zip->open($zip_file_name, \ZipArchive::CREATE)) {
            $tsv_file_name = basename($tsv_file_path);
            $zip->addFile($tsv_file_path, $tsv_file_name);
            $zip->close();
        }
        \File::delete($end_file_path, $tsv_file_path);
        $files = \File::allFiles(config('settings.data_link.master.products.zip_file_path'));
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
            array_merge([config('settings.data_link.master.products.program_name')], $contents)
        );
    }
}
