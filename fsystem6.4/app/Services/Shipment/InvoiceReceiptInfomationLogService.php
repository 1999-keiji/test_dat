<?php

namespace App\Services\Shipment;

use stdClass;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use Cake\Chronos\Chronos;
use setasign\Fpdi\TcpdfFpdi;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Repositories\Shipment\InvoiceReceiptInfomationLogRepository;
use App\ValueObjects\Date\DeliveryDate;

class InvoiceReceiptInfomationLogService
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
     * @var \App\Repositories\Order\InvoiceReceiptInfomationLogRepository
     */
    private $invoice_receipt_infomation_log_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @param  \App\Repositories\Shipment\InvoiceReceiptInfomationLogRepository $invoice_receipt_infomation_log_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Filesystem $file,
        InvoiceReceiptInfomationLogRepository $invoice_receipt_infomation_log_repo
    ) {
        $this->db = $db;
        $this->file = $file;
        $this->invoice_receipt_infomation_log_repo = $invoice_receipt_infomation_log_repo;
    }

    /**
     * 出荷案内書の出力
     *
     * @param  array $order_numbers
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  array $grouped_orders
     */
    public function exportReceiptFiles(
        array $order_numbers,
        Factory $factory,
        Customer $customer,
        array $grouped_orders
    ) {
        $delivery_config = config('constant.shipment.form_output.delivery_pdf');
        $receipt_config = config('constant.shipment.form_output.receipt_pdf');
        $save_path = config('constant.get_template.global.save_path');

        $delivery_template_file = $delivery_config['template_name'];
        if ($customer->is_default_customer) {
            $delivery_template_file = sprintf($delivery_template_file, $factory->factory_code);
        }
        if (! $customer->is_default_customer) {
            $delivery_template_file = sprintf(
                $delivery_template_file,
                implode('_', [$factory->factory_code, 'other_customer'])
            );
        }

        $delivery_template_path = config('constant.get_template.shipment.pdf_form.delivery_path').
            $delivery_template_file;
        if (! $this->file->exists($delivery_template_path)) {
            throw new TemplateFileDoesNotExistException(
                'target template file does not exists: '.$delivery_template_path
            );
        }

        $receipt_template_file = $receipt_config['template_name'];
        if ($customer->is_default_customer) {
            $receipt_template_file = sprintf($receipt_template_file, '');
        }
        if (! $customer->is_default_customer) {
            $receipt_template_file = sprintf($receipt_template_file, '_other_customer');
        }

        $receipt_template_path = config('constant.get_template.shipment.pdf_form.receipt_path').
            $receipt_template_file;
        if (! $this->file->exists($receipt_template_path)) {
            throw new TemplateFileDoesNotExistException(
                'target template file does not exists: '.$receipt_template_path
            );
        }

        $pdf_writer = new TcpdfFpdi();
        foreach ($grouped_orders as $group) {
            // 納品書
            $font = $delivery_config['font_family'];
            $coordinates = $delivery_config['coordinates'];

            $pdf_writer->AddPage();
            $pdf_writer->setSourceFile($delivery_template_path);
            $pdf_writer->useTemplate($pdf_writer->importPage(1), null, null, null, null, true);
            $pdf_writer->SetAutoPageBreak(false, 0);
            $pdf_writer->setPrintHeader(false);
            $pdf_writer->setPrintFooter(false);

            if ($group->print_state !== '') {
                $pdf_writer->SetFont($font, '', $coordinates['downloaded_mark']['font_size']);
                $pdf_writer->SetXY($coordinates['downloaded_mark']['x'], $coordinates['downloaded_mark']['y']);
                $pdf_writer->Write(0, $coordinates['downloaded_mark']['text']);
            }

            $pdf_writer->SetFont($font, '', $coordinates['delivery_destination']['font_size']);
            $pdf_writer->SetXY($coordinates['delivery_destination']['x'], $coordinates['delivery_destination']['y']);
            $pdf_writer->Write(
                0,
                sprintf($coordinates['delivery_destination']['text'], $group->delivery_destination_name)
            );

            $pdf_writer->SetFont($font, '', $coordinates['end_user']['font_size']);
            $pdf_writer->SetXY($coordinates['end_user']['x'], $coordinates['end_user']['y']);
            $pdf_writer->Write(0, $group->end_user_name);

            $pdf_writer->SetFont($font, '', $coordinates['shipping_date']['font_size']);
            $pdf_writer->SetXY($coordinates['shipping_date']['x'], $coordinates['shipping_date']['y']);
            $pdf_writer->Write(0, $group->shipping_date->format('Y/m/d'));

            if ($customer->is_default_customer) {
                $pdf_writer->SetFont($font, '', $coordinates['customer_postal_code']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['customer_postal_code']['x'],
                    $coordinates['customer_postal_code']['y']
                );
                $pdf_writer->Write(0, sprintf($coordinates['customer_postal_code']['text'], $customer->postal_code));

                $pdf_writer->SetFont($font, '', $coordinates['customer_address']['font_size']);
                $pdf_writer->SetXY($coordinates['customer_address']['x'], $coordinates['customer_address']['y']);
                $pdf_writer->Write(0, $customer->address);

                $pdf_writer->SetFont($font, '', $coordinates['customer_name']['font_size']);
                $pdf_writer->SetXY($coordinates['customer_name']['x'], $coordinates['customer_name']['y']);
                $pdf_writer->Write(0, $customer->customer_name);

                $pdf_writer->SetFont($font, '', $coordinates['seller_name']['default']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['seller_name']['default']['x'],
                    $coordinates['seller_name']['default']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['seller_name']['default']['text'],
                    $group->seller_name ?? ''
                ));

                $pdf_writer->SetFont($font, '', $coordinates['customer_phone_number']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['customer_phone_number']['x'],
                    $coordinates['customer_phone_number']['y']
                );
                $pdf_writer->Write(0, sprintf($coordinates['customer_phone_number']['text'], $customer->phone_number));

                $pdf_writer->SetFont($font, '', $coordinates['factory_postal_code']['default']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_postal_code']['default']['x'],
                    $coordinates['factory_postal_code']['default']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['factory_postal_code']['default']['text'],
                    $factory->postal_code
                ));

                $pdf_writer->SetFont($font, '', $coordinates['factory_address']['default']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_address']['default']['x'],
                    $coordinates['factory_address']['default']['y']
                );
                $pdf_writer->Write(0, $factory->address);

                [$corporation_name, $factory_name] = explode(' ', $factory->factory_name);

                $pdf_writer->SetFont($font, '', $coordinates['corporation_name']['default']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['corporation_name']['default']['x'],
                    $coordinates['corporation_name']['default']['y']
                );
                $pdf_writer->Write(0, $corporation_name);

                $pdf_writer->SetFont($font, '', $coordinates['factory_name']['default']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_name']['default']['x'],
                    $coordinates['factory_name']['default']['y']
                );
                $pdf_writer->Write(0, $factory_name);
/* GGNを外す
                $pdf_writer->SetFont($font, '', $coordinates['global_gap_number']['default']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['global_gap_number']['default']['x'],
                    $coordinates['global_gap_number']['default']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['global_gap_number']['default']['text'],
                    $factory->global_gap_number
                ));
*/
            }

            if (! $customer->is_default_customer) {
                $pdf_writer->SetFont($font, '', $coordinates['factory_postal_code']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_postal_code']['other']['x'],
                    $coordinates['factory_postal_code']['other']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['factory_postal_code']['other']['text'],
                    $factory->postal_code
                ));

                $pdf_writer->SetFont($font, '', $coordinates['factory_address']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_address']['other']['x'],
                    $coordinates['factory_address']['other']['y']
                );
                $pdf_writer->Write(0, $factory->address);

                [$corporation_name, $factory_name] = explode(' ', $factory->factory_name);

                $pdf_writer->SetFont($font, '', $coordinates['corporation_name']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['corporation_name']['other']['x'],
                    $coordinates['corporation_name']['other']['y']
                );
                $pdf_writer->Write(0, $corporation_name);

                $pdf_writer->SetFont($font, '', $coordinates['factory_name']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_name']['other']['x'],
                    $coordinates['factory_name']['other']['y']
                );
                $pdf_writer->Write(0, $factory_name);

                $pdf_writer->SetFont($font, '', $coordinates['seller_name']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['seller_name']['other']['x'],
                    $coordinates['seller_name']['other']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['seller_name']['other']['text'],
                    $group->seller_name ?? ''
                ));
/* GGNを外す
                $pdf_writer->SetFont($font, '', $coordinates['global_gap_number']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['global_gap_number']['other']['x'],
                    $coordinates['global_gap_number']['other']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['global_gap_number']['other']['text'],
                    $factory->global_gap_number
                ));
*/
                $pdf_writer->SetFont($font, '', $coordinates['factory_phone_number']['other']['font_size']);
                $pdf_writer->SetXY(
                    $coordinates['factory_phone_number']['other']['x'],
                    $coordinates['factory_phone_number']['other']['y']
                );
                $pdf_writer->Write(0, sprintf(
                    $coordinates['factory_phone_number']['other']['text'],
                    $factory->phone_number
                ));
            }

            $pdf_writer = $this->writeFileTable($pdf_writer, $delivery_config, $group);

            // 受領書
            $font = $receipt_config['font_family'];
            $coordinates = $receipt_config['coordinates'];

            $pdf_writer->AddPage();
            $pdf_writer->setSourceFile($receipt_template_path);
            $pdf_writer->useTemplate($pdf_writer->importPage(1), null, null, null, null, true);
            $pdf_writer->SetAutoPageBreak(false, 0);
            $pdf_writer->setPrintHeader(false);
            $pdf_writer->setPrintFooter(false);

            if ($group->print_state !== '') {
                $pdf_writer->SetFont($font, '', $coordinates['downloaded_mark']['font_size']);
                $pdf_writer->SetXY($coordinates['downloaded_mark']['x'], $coordinates['downloaded_mark']['y']);
                $pdf_writer->Write(0, $coordinates['downloaded_mark']['text']);
            }

            if ($customer->is_default_customer) {
                $pdf_writer->SetFont($font, '', $coordinates['customer']['font_size']);
                $pdf_writer->SetXY($coordinates['customer']['x'], $coordinates['customer']['y']);
                $pdf_writer->Write(0, sprintf($coordinates['customer']['text'], $customer->customer_name));

                $pdf_writer->SetFont($font, '', $coordinates['factory']['font_size']);
                $pdf_writer->SetXY($coordinates['factory']['x'], $coordinates['factory']['y']);
                $pdf_writer->Write(0, $factory->factory_name);
            }

            if (! $customer->is_default_customer) {
                $pdf_writer->SetFont($font, '', $coordinates['customer']['font_size']);
                $pdf_writer->SetXY($coordinates['customer']['x'], $coordinates['customer']['y']);
                $pdf_writer->Write(0, sprintf($coordinates['customer']['text'], $factory->factory_name));
            }

            $pdf_writer->SetFont($font, '', $coordinates['shipping_date']['font_size']);
            $pdf_writer->SetXY($coordinates['shipping_date']['x'], $coordinates['shipping_date']['y']);
            $pdf_writer->Write(0, $group->shipping_date->format('Y/m/d'));

            $pdf_writer->SetFont($font, '', $coordinates['delivery_destination_postal_code']['font_size']);
            $pdf_writer->SetXY(
                $coordinates['delivery_destination_postal_code']['x'],
                $coordinates['delivery_destination_postal_code']['y']
            );
            $pdf_writer->Write(
                0,
                sprintf(
                    $coordinates['delivery_destination_postal_code']['text'],
                    $group->delivery_destination_postal_code
                )
            );

            $pdf_writer->SetFont($font, '', $coordinates['delivery_destination_address']['font_size']);
            if (mb_strwidth($group->delivery_destination_address) > 30) {
                $pdf_writer->SetFont($font, '', $coordinates['delivery_destination_address']['font_size_small']);
            }

            $pdf_writer->SetXY(
                $coordinates['delivery_destination_address']['x'],
                $coordinates['delivery_destination_address']['y']
            );
            $pdf_writer->Write(0, $group->delivery_destination_address);

            $pdf_writer->SetFont($font, '', $coordinates['delivery_destination_name']['font_size']);
            if (mb_strwidth($group->delivery_destination_name) > 30) {
                $pdf_writer->SetFont($font, '', $coordinates['delivery_destination_name']['font_size_small']);
            }

            $pdf_writer->SetXY(
                $coordinates['delivery_destination_name']['x'],
                $coordinates['delivery_destination_name']['y']
            );
            $pdf_writer->Write(0, $group->delivery_destination_name);

            $pdf_writer->SetFont($font, '', $coordinates['delivery_destination_phone_number']['font_size']);
            $pdf_writer->SetXY(
                $coordinates['delivery_destination_phone_number']['x'],
                $coordinates['delivery_destination_phone_number']['y']
            );
            $pdf_writer->Write(
                0,
                sprintf(
                    $coordinates['delivery_destination_phone_number']['text'],
                    $group->delivery_destination_phone_number
                )
            );

            $pdf_writer = $this->writeFileTable($pdf_writer, $receipt_config, $group);
        }

        $this->db->transaction(function () use ($order_numbers) {
            $this->invoice_receipt_infomation_log_repo->create($order_numbers);
        });

        return [
            'file' => $pdf_writer,
            'name' => generate_file_name($receipt_config['zip_name'], [
                $factory->factory_abbreviation,
                $customer->customer_abbreviation
            ])
        ];
    }

    /**
     * 帳票の表部分書き込み
     *
     * @param  \setasign\Fpdi\TcpdfFpdi $pdf_writer
     * @param  array $config
     * @param  stdClass $order_group
     * @return \setasign\Fpdi\TcpdfFpdi $pdf_writer
     */
    private function writeFileTable(
        TcpdfFpdi $pdf_writer,
        array $config,
        stdClass $order_group
    ): TcpdfFpdi {
        $disabled_to_print_amount =
            $order_group->disabled_to_display_price ||
            $order_group->orders->isDisabledToPrintAmount();

        $font = $config['font_family'];
        $coordinates = $config['coordinates'];

        $current_y = $config['coordinates']['table']['base_y'];
        foreach ($order_group->orders as $o) {
            $pdf_writer->SetFont($font, '', $coordinates['table']['font_size']);
            $position_y = $current_y;

            $product_name = $o->getProductNameOnShipmentFile();
            if (mb_strwidth($product_name, 'UTF-8') > 28) {
                $pdf_writer->SetFont($font, '', $coordinates['table']['font_size_small']);
                $position_y = $position_y + 0.5;
            }

            $pdf_writer->SetXY($coordinates['table']['product_name_x'], $position_y);
            $pdf_writer->Write(0, $product_name);

            $pdf_writer->SetFont($font, '', $coordinates['table']['font_size']);
            $pdf_writer->SetXY($coordinates['table']['order_quantity_x'], $current_y);
            $pdf_writer->Cell($coordinates['table']['order_quantity_width'], 0, $o->order_quantity, 0, 0, 'R');

            $pdf_writer->SetXY($coordinates['table']['delivery_quantity_x'], $current_y);
            $pdf_writer->Cell($coordinates['table']['delivery_quantity_width'], 0, $o->order_quantity, 0, 0, 'R');

            $pdf_writer->SetXY($coordinates['table']['unit_x'], $current_y);
            $pdf_writer->Write(0, $o->place_order_unit_code ?: $o->unit);

            $pdf_writer->SetXY($coordinates['table']['delivery_date_x'], $current_y);
            $pdf_writer->Write(0, DeliveryDate::parse($o->delivery_date)->format('m/d'));

            $order_unit = $disabled_to_print_amount ? '' : number_format((float)$o->received_order_unit);
            $pdf_writer->SetXY($coordinates['table']['order_unit_x'], $current_y);
            $pdf_writer->Cell($coordinates['table']['cell_width'], 0, $order_unit, 0, 0, 'R');

            $order_amount = $disabled_to_print_amount ? '' : number_format((float)$o->customer_received_order_amount);
            $pdf_writer->SetXY($coordinates['table']['order_amount_x'], $current_y);
            $pdf_writer->Cell($coordinates['table']['cell_width'], 0, $order_amount, 0, 0, 'R');

            $pdf_writer->SetXY($coordinates['table']['end_user_order_number_x'], $current_y);
            $pdf_writer->Write(0, $o->end_user_order_number);

            $pdf_writer->SetXY($coordinates['table']['order_number_x'], $current_y);
            $pdf_writer->Write(0, $o->order_number);

            $pdf_writer->SetFont($font, '', $coordinates['table']['font_size_small']);
            $pdf_writer->SetXY($coordinates['table']['remark_x'], $current_y - 1.8);
            $pdf_writer->Write(0, $o->getBasePlusOrderNumber());
            $pdf_writer->SetXY($coordinates['table']['remark_x'], $current_y + 1.8);
            $pdf_writer->Write(0, preg_replace(['/\r\n/','/\r/','/\n/'], ' ', $o->order_message));

            $current_y += $coordinates['table']['add_y'];
        }

        if (! $disabled_to_print_amount) {
            $sum_of_tax_amount = $order_group->orders->pluck('tax_amount')->sum();
            $pdf_writer->SetFont($font, 'B', $coordinates['tax_amount']['font_size']);
            $pdf_writer->SetXY($coordinates['tax_amount']['x'], $coordinates['tax_amount']['y']);
            $pdf_writer->Cell($coordinates['tax_amount']['width'], 0, number_format($sum_of_tax_amount), 0, 0, 'R');

            $sum_of_amount = $order_group->orders->pluck('customer_received_order_amount')->sum();
            $pdf_writer->SetFont($font, 'B', $coordinates['tax_included']['font_size']);
            $pdf_writer->SetXY($coordinates['tax_included']['x'], $coordinates['tax_included']['y']);
            $pdf_writer->Cell(
                $coordinates['tax_included']['width'],
                0,
                number_format($sum_of_amount + $sum_of_tax_amount),
                0,
                0,
                'R'
            );
        }

        return $pdf_writer;
    }
}
