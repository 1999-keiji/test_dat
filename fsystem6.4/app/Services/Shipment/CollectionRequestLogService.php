<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use DateTime;
use ZipArchive;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Repositories\Shipment\CollectionRequestLogRepository;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;

class CollectionRequestLogService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $file;

    /**
     * @var \App\Repositories\Shipment\CollectionRequestLogRepository
     */
    private $collection_request_log_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @param  \App\Repositories\Shipment\CollectionRequestLogRepository $collection_request_log_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Filesystem $file,
        CollectionRequestLogRepository $collection_request_log_repo
    ) {
        $this->db = $db;
        $this->file = $file;
        $this->collection_request_log_repo = $collection_request_log_repo;
    }

    /**
     * 集荷依頼書の出力
     *
     * @param array $order_numbers
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\Customer $customer
     * @param array $grouped_orders
     */
    public function exportCollectionRequestFiles(
        array $order_numbers,
        Factory $factory,
        Customer $customer,
        array $grouped_orders
    ) {
        $template_path = config('constant.get_template.shipment.excel_form.collection_request_path');
        if (! $this->file->exists($template_path)) {
            $message = sprintf('not found the file: %', $template_path);
            throw new TemplateFileDoesNotExistException($message);
        }

        $save_file_path = config('constant.get_template.global.save_path');

        $collection_request_file_names = [];
        foreach ($grouped_orders as $group) {
            $shipping_date = ShippingDate::parse($group->shipping_date);
            $file_name = generate_file_name(trans('view.shipment.collection_request.index'), [
                "[{$shipping_date->format('Ymd')}]",
                "[{$group->transport_company_abbreviation}]",
                "[{$group->collection_time}]"
            ]);

            $reader = PHPExcel_IOFactory::createReader('Excel2007');
            $excel = $reader->load($template_path);

            $chunked_orders = $group->orders->chunk(20);
            foreach (range($chunked_orders->count(), 4) as $idx) {
                $excel->removeSheetByIndex($chunked_orders->count());
            }

            $total_page = $chunked_orders->count();
            foreach ($chunked_orders as $idx => $orders) {
                $current_page =  $idx + 1;

                $excel->setActiveSheetIndex($idx);
                $sheet = $excel->getActiveSheet();

                $sheet->setCellValue('A2', implode(' ', [
                    $group->transport_company_name,
                    $group->transport_branch_name,
                    '御中'
                ]));

                $sheet->setCellValue('C3', $group->tarnsport_company_phone_number);
                $sheet->setCellValue('C4', $group->transport_company_fax_number);
                $sheet->setCellValue('D6', PHPExcel_Shared_Date::PHPToExcel(new DateTime($shipping_date->value())));
                $sheet->setCellValue('D8', $group->collection_time);

                $sheet->setCellValue('AT3', sprintf('%d/%d', $current_page, $total_page));
                $sheet->setCellValue('AK6', $factory->factory_name);
                $sheet->setCellValue('AK8', '〒'.$factory->postal_code);
                $sheet->setCellValue('AK9', $factory->address);
                $sheet->setCellValue('AM10', $factory->phone_number);
                $sheet->setCellValue('AM11', $factory->fax_number);

                $row = 13;
                foreach ($orders as $order_idx => $o) {
                    $sheet->setCellValue('A'.$row, $order_idx + 1);
                    $sheet->setCellValue(
                        'B'.$row,
                        str_replace(['株式会社', '（株）', '(株)'], '', $o->delivery_destination_name)
                    );
                    $sheet->setCellValue('L'.$row, $o->delivery_destination_address);
                    $sheet->setCellValue('Z'.$row, $o->product_name);
                    $sheet->setCellValue(
                        'AH'.$row,
                        number_format_of_product(convert_to_kilogram($o->product_weight_per_case), 'weight').'kg'
                    );
                    $sheet->setCellValue(
                        'AK'.$row,
                        PHPExcel_Shared_Date::PHPToExcel(new DateTime($o->printing_delivery_date->value()))
                    );
                    $sheet->setCellValue('AP'.$row, $o->order_quantity);
                    $sheet->setCellValue('AR'.$row, $o->getPackingQuantity());
                    $sheet->setCellValue('AT'.$row, $o->collection_request_remark);

                    $row = $row + 1;
                }
            }

            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $writer->save($save_file_path.$file_name.'.xlsx');

            $collection_request_file_names[] = $file_name;
        }

        $this->db->transaction(function () use ($order_numbers) {
            $this->collection_request_log_repo->create($order_numbers);
        });

        $zip = new ZipArchive();
        $zip_name = generate_file_name(trans('view.shipment.collection_request.index'), [
            $factory->factory_abbreviation,
            $customer->customer_abbreviation
        ]).'.zip';

        $zip->open($save_file_path.$zip_name, ZIPARCHIVE::CREATE);
        foreach ($collection_request_file_names as $file) {
            $zip->addFile($save_file_path.$file.'.xlsx', $file.'.xlsx');
        }

        $zip->close();
        foreach ($collection_request_file_names as $file) {
            $this->file->delete($save_file_path.$file.'.xlsx');
        }

        response_to_download_zip($save_file_path, $zip_name);

        $this->file->delete($save_file_path.$zip_name);
        exit;
    }
}
