<?php

namespace App\Services\Shipment;

use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use setasign\Fpdi\TcpdfFpdi;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Repositories\Shipment\ShipmentInfomationLogRepository;
use App\ValueObjects\Date\DeliveryDate;

class ShipmentInfomationLogService
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
     * @var \App\Repositories\Shipment\ShipmentInfomationLogRepository
     */
    private $shipment_infomation_log_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @param  \App\Repositories\Shipment\ShipmentInfomationLogRepository $shipment_infomation_log_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Filesystem $file,
        ShipmentInfomationLogRepository $shipment_infomation_log_repo
    ) {
        $this->db = $db;
        $this->file = $file;
        $this->shipment_infomation_log_repo = $shipment_infomation_log_repo;
    }

    /**
     * 出荷案内書の出力
     *
     * @param  array $order_numbers
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Customer $customer
     * @param  array $grouped_orders
     * @return array
     */
    public function exportShipmentFiles(
        array $order_numbers,
        Factory $factory,
        Customer $customer,
        array $grouped_orders
    ): array {
        $config = config('constant.shipment.form_output.shipment_pdf');
        $save_path = config('constant.get_template.global.save_path');

        $template_file = $config['template_name'];
        if ($customer->is_default_customer) {
            $template_file = sprintf($template_file, $factory->factory_code);
        }
        if (! $customer->is_default_customer) {
            $template_file = sprintf($template_file, implode('_', [$factory->factory_code, 'other_customer']));
        }

        $template_path = config('constant.get_template.shipment.pdf_form.shipment_path').$template_file;
        if (! $this->file->exists($template_path)) {
            throw new TemplateFileDoesNotExistException(
                'target template file does not exists: '.$template_path
            );
        }

        $pdf_writer = new TcpdfFpdi();
        foreach ($grouped_orders as $group) {
            $font = $config['font_family'];
            $coordinates = $config['coordinates'];

            $pdf_writer->AddPage();
            $pdf_writer->setSourceFile($template_path);
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

            $current_y = $coordinates['table']['base_y'];
            foreach ($group->orders as $o) {
                $pdf_writer->SetFont($font, '', $coordinates['table']['font_size']);
                $position_y = $current_y;

                $product_name = $o->getProductNameOnShipmentFile();
                if (mb_strwidth($product_name, 'UTF-8') > 28) {
                    $pdf_writer->SetFont($font, '', $coordinates['table']['font_size_more_small']);
                    $position_y = $position_y + 0.5;
                }

                $pdf_writer->SetXY($coordinates['table']['product_name_x'], $position_y);
                $pdf_writer->Write(0, $product_name);

                $pdf_writer->SetFont($font, '', $coordinates['table']['font_size']);
                $pdf_writer->SetXY($coordinates['table']['order_quantity_x'], $current_y);
                $pdf_writer->Cell($coordinates['table']['order_quantity_width'], 0, $o->order_quantity, 0, 0, 'R');

                $pdf_writer->SetXY($coordinates['table']['unit_x'], $current_y);
                $pdf_writer->Write(0, $o->place_order_unit_code ?: $o->unit);

                $pdf_writer->SetXY($coordinates['table']['delivery_date_x'], $current_y);
                $pdf_writer->Write(0, DeliveryDate::parse($o->delivery_date)->format('m/d'));

                $pdf_writer->SetXY($coordinates['table']['end_user_order_number_x'], $current_y);
                $pdf_writer->Write(0, $o->end_user_order_number);

                $pdf_writer->SetXY($coordinates['table']['order_number_x'], $current_y);
                $pdf_writer->Write(0, $o->order_number);

                $pdf_writer->SetFont($font, '', $coordinates['table']['font_size_small']);
                $pdf_writer->SetXY($coordinates['table']['remark_x'], $current_y - 2);
                $pdf_writer->Write(0, $o->getBasePlusOrderNumber());
                $pdf_writer->SetXY($coordinates['table']['remark_x'], $current_y + 2);
                $pdf_writer->Write(0, preg_replace(['/\r\n/','/\r/','/\n/'], ' ', $o->order_message));

                $current_y += $coordinates['table']['add_y'];
            }
        }

        $this->db->transaction(function () use ($order_numbers) {
            $this->shipment_infomation_log_repo->create($order_numbers);
        });

        return [
            'file' => $pdf_writer,
            'name' => generate_file_name($config['file_name'], [
                $factory->factory_abbreviation,
                $customer->customer_abbreviation
            ])
        ];
    }
}
