<?php

declare(strict_types=1);

namespace App\Services\Shipment;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use setasign\Fpdi\TcpdfFpdi;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\TemplateFileDoesNotExistException;
use App\Models\Master\Customer;
use App\Models\Master\Factory;
use App\Models\Shipment\Invoice;
use App\Models\Order\Collections\OrderCollection;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Shipment\BillLogRepository;
use App\Repositories\Shipment\InvoiceRepository;
use App\ValueObjects\Date\DeliveryDate;

class InvoiceService
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
     * @var \App\Repositories\Shipment\InvoiceRepository
     */
    private $invoice_repo;

    /**
     * @var \App\Repositories\Shipment\BillLogRepository
     */
    private $bill_log_repo;

    /**
     * @var \App\Repositories\Order\OrderRepository
     */
    private $order_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @param  \App\Repositories\Shipment\InvoiceRepository $invoice_repo
     * @param  \App\Repositories\Shipment\BillLogRepository $bill_log_repo
     * @param  \App\Repositories\Order\OrderRepository $order_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        Filesystem $file,
        InvoiceRepository $invoice_repo,
        BillLogRepository $bill_log_repo,
        OrderRepository $order_repo
    ) {
        $this->db = $db;
        $this->file = $file;
        $this->invoice_repo = $invoice_repo;
        $this->bill_log_repo = $bill_log_repo;
        $this->order_repo = $order_repo;
    }

    /**
     * 請求書データの検索
     *
     * @param  array $params
     * @param  \App\Models\Master\Customer $customer
     * @param  array $order
     * @return \Illuminate\Database\Eloquent\Collection;
     */
    public function searchInvoices(array $params, Customer $customer, ?array $order = []): Collection
    {
        if ($delivery_month = $params['delivery_month'] ?? null) {
            $delivery_month =  DeliveryDate::createFromYearMonth($delivery_month);
        }

        return $this->invoice_repo->searchInvoices($params, $customer, $delivery_month, $order);
    }

    /**
     * 締め処理済の請求書情報を取得
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @return \App\Models\Shipment\Invoice|null
     */
    public function getFixedInvoice(array $params, DeliveryDate $delivery_month): ?Invoice
    {
        return $this->invoice_repo->getFixedInvoice($params, $delivery_month);
    }

    /**
     * 請求書締めの実行
     *
     * @param  array $params
     * @param  \App\Models\Master\Customer $customer
     * @return void
     */
    public function fixInvoice(array $params, Customer $customer): void
    {
        $this->db->transaction(function () use ($params, $customer) {
            $invoice = $this->getFixedInvoice($params, DeliveryDate::createFromYearMonth($params['delivery_month']));
            if (! is_null($invoice)) {
                $message = 'target invoice has fixed already. factory_code: %s, customer_code: %s, delivery_month: %s';
                throw new OptimisticLockException(sprintf(
                    $message,
                    $params['factory_code'],
                    $params['customer_code'],
                    $params['delivery_month']
                ));
            }

            $invoice = $this->invoice_repo->createInvoice($params);
            $aggregated = $this->order_repo->updateInvoiceNumber($invoice, $customer);

            $invoice->order_quantity = $aggregated->order_quantity;
            $invoice->order_amount = $aggregated->order_amount;
            $invoice->save();
        });
    }

    /**
     * 請求書締めの解除
     *
     * @param  \App\Models\Shipment\Invoice $invoice
     * @return void
     */
    public function cancelInvoice(Invoice $invoice): void
    {
        if (! $invoice->hasFixed()) {
            $message = 'target invoice has canceled already. invoice_number: %s';
            throw new OptimisticLockException(sprintf($message, $invoice->invoice_number));
        }

        $this->order_repo->cancelInvoiceNumber($invoice);

        $invoice->has_fixed = false;
        $invoice->save();
    }

    /**
     * 請求書ファイルの生成
     *
     * @param   \App\Models\Master\Factory $factory
     * @param   \App\Models\Master\Customer $customer
     * @param   \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @param   \App\Models\Shipment\Invoice $invoice
     * @param   \App\Models\Order\Collections\OrderCollection
     * @return  array
     */
    public function createInvoiceFile(
        Factory $factory,
        Customer $customer,
        DeliveryDate $delivery_month,
        ?Invoice $invoice,
        OrderCollection $orders
    ) {
        $config = config('constant.shipment.invoice');

        $cover_template_path = config('constant.get_template.shipment.pdf_form.invoice_path').
            sprintf($config['cover_item']['format'], $factory->factory_code);
        if (! $this->file->exists($cover_template_path)) {
            throw new TemplateFileDoesNotExistException(
                'target template file does not exists: '.$cover_template_path
            );
        }

        $detail_template_path = config('constant.get_template.shipment.pdf_form.invoice_path').
            sprintf($config['detail_item']['format'], $factory->factory_code);
        if (! $this->file->exists($detail_template_path)) {
            throw new TemplateFileDoesNotExistException(
                'target template file does not exists: '.$detail_template_path
            );
        }

        $grouped_per_cover = $orders->groupToInvoiceCover($config['cover_item']['max']);
        $grouped_per_detail = $orders->groupToInvoiceDetail($config['detail_item']['max']);

        $number_of_pages = $grouped_per_cover->count() +
            $grouped_per_detail->reduce(function ($number_of_pages, $group) {
                return $number_of_pages + $group->chunked_orders->count();
            }, 0);

        $pdf = new TcpdfFpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        foreach ($grouped_per_cover as $idx => $chunked) {
            $pdf->AddPage();
            $pdf->setSourceFile($cover_template_path);
            $pdf->useTemplate($pdf->importPage(1), null, null, null, null, true);

            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_middle']);

            $customer_name_config = $config['cover_item']['customer_name'];
            $pdf->SetXY($customer_name_config['x'], $customer_name_config['y']);
            $pdf->MultiCell(
                $customer_name_config['width'],
                0,
                $customer->customer_name.$customer_name_config['suffix'],
                $config['cover_item']['border'],
                'L'
            );

            $invoice_date_config = $config['cover_item']['invoice_date'];
            $pdf->SetXY($invoice_date_config['x'], $invoice_date_config['y']);
            $pdf->MultiCell(
                $invoice_date_config['width'],
                0,
                $delivery_month->now()->format($invoice_date_config['format']),
                $config['cover_item']['border'],
                'R'
            );

            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_small']);

            $invoice_number_config = $config['cover_item']['invoice_number'];
            $pdf->SetXY($invoice_number_config['x'], $invoice_number_config['y']);
            $pdf->MultiCell(
                $invoice_number_config['width'],
                0,
                $invoice->invoice_number ?? '',
                $config['cover_item']['border'],
                'R'
            );

            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_middle']);

            $delivery_date_config = $config['cover_item']['delivery_date'];
            $pdf->SetXY($delivery_date_config['x'], $delivery_date_config['y']);
            $pdf->MultiCell(
                $delivery_date_config['width'],
                0,
                sprintf(
                    $delivery_date_config['format'],
                    $customer->getFirstOfDeliveryDateOfInvoice($delivery_month)->format('n/j'),
                    $customer->getEndOfDeliveryDateOfInvoice($delivery_month)->format('n/j')
                ),
                $config['cover_item']['border'],
                'R'
            );

            $pdf->SetFont($config['font_family'], 'B', $config['cover_item']['text_large']);

            $invoice_amount_config = $config['cover_item']['invoice_amount'];
            $pdf->SetXY($invoice_amount_config['x'], $invoice_amount_config['y']);
            $pdf->MultiCell(
                $invoice_amount_config['width'],
                0,
                $this->formatNumber($orders->sum('order_amount_with_tax')),
                $config['cover_item']['border'],
                'R'
            );

            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_middle']);

            $factory_postal_code_config = $config['cover_item']['factory_postal_code'];
            $pdf->SetXY($factory_postal_code_config['x'], $factory_postal_code_config['y']);
            $pdf->MultiCell(
                $factory_postal_code_config['width'],
                0,
                sprintf($factory_postal_code_config['format'], $factory->invoice_postal_code),
                $config['cover_item']['border'],
                'L'
            );

            $factory_postal_address_config = $config['cover_item']['factory_address'];
            $pdf->SetXY($factory_postal_address_config['x'], $factory_postal_address_config['y']);
            $pdf->MultiCell(
                $factory_postal_address_config['width'],
                0,
                $factory->invoice_address,
                $config['cover_item']['border'],
                'L'
            );

            $factory_phone_number_config = $config['cover_item']['factory_phone_number'];
            $pdf->SetXY($factory_phone_number_config['x'], $factory_phone_number_config['y']);
            $pdf->MultiCell(
                $factory_phone_number_config['width'],
                0,
                sprintf($factory_phone_number_config['format'], $factory->invoice_phone_number),
                $config['cover_item']['border'],
                'L'
            );

            $factory_fax_number_config = $config['cover_item']['factory_fax_number'];
            $pdf->SetXY($factory_fax_number_config['x'], $factory_fax_number_config['y']);
            $pdf->MultiCell(
                $factory_fax_number_config['width'],
                0,
                sprintf($factory_fax_number_config['format'], $factory->invoice_fax_number),
                $config['cover_item']['border'],
                'L'
            );
/* GGN外す
            $global_gap_number_config = $config['cover_item']['global_gap_number'];
            $pdf->SetXY($global_gap_number_config['x'], $global_gap_number_config['y']);
            $pdf->MultiCell(
                $global_gap_number_config['width'],
                0,
                sprintf($global_gap_number_config['format'], $factory->global_gap_number),
                $config['cover_item']['border'],
                'L'
            );
*/
            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_small']);

            $currency_config = $config['cover_item']['currency'];
            $pdf->SetXY($currency_config['x'], $currency_config['y']);
            $pdf->MultiCell(
                $currency_config['width'],
                0,
                $orders->first()->currency_code,
                $config['cover_item']['border'],
                'C'
            );

            $height = $config['cover_item']['order_details']['height'];
            $width = $config['cover_item']['order_details']['width'];
            foreach ($chunked->values() as $group_index => $group) {
                $col = $config['cover_item']['order_details']['base']['x'];
                $row = ($group_index * $height['outer']) + $config['cover_item']['order_details']['base']['y'];

                $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_small']);
                $pdf->MultiCell(
                    $width['end_user_name'],
                    $height['inner'],
                    $group->end_user_name,
                    $config['cover_item']['border'],
                    'L',
                    false,
                    1,
                    $col,
                    $row,
                    true,
                    0,
                    false,
                    true,
                    $height['inner'],
                    'M',
                    true
                );

                $col += $width['end_user_name'] + $width['border'];
                $pdf->MultiCell(
                    $width['delivery_destination_name'],
                    $height['inner'],
                    $group->delivery_destination_name,
                    $config['cover_item']['border'],
                    'L',
                    false,
                    1,
                    $col,
                    $row,
                    true,
                    0,
                    false,
                    true,
                    $height['inner'],
                    'M',
                    true
                );

                $col += $width['delivery_destination_name'] + $width['border'];
                $pdf->MultiCell(
                    $width['sub_total'],
                    $height['inner'],
                    $this->formatNumber($group->sum_of_amount),
                    $config['cover_item']['border'],
                    'R',
                    false,
                    1,
                    $col,
                    $row,
                    true,
                    0,
                    false,
                    true,
                    $height['inner'],
                    'M'
                );

                $col += $width['sub_total'] + $width['border'];
                $pdf->MultiCell(
                    $width['tax_total'],
                    $height['inner'],
                    $this->formatNumber($group->sum_of_tax),
                    $config['cover_item']['border'],
                    'R',
                    false,
                    1,
                    $col,
                    $row,
                    true,
                    0,
                    false,
                    true,
                    $height['inner'],
                    'M'
                );

                $col += $width['tax_total'] + $width['border'];
                $pdf->MultiCell(
                    $width['total'],
                    $height['inner'],
                    $this->formatNumber($group->sum_of_amount_with_tax),
                    $config['cover_item']['border'],
                    'R',
                    false,
                    1,
                    $col,
                    $row,
                    true,
                    0,
                    false,
                    true,
                    $height['inner'],
                    'M'
                );

                $col += $width['total'] + $width['separate_line'] + ($width['border'] * 2);
                $pdf->MultiCell(
                    $width['weight_total'],
                    $height['inner'],
                    $this->formatWeight($group->sum_of_weight),
                    $config['cover_item']['border'],
                    'R',
                    false,
                    1,
                    $col,
                    $row,
                    true,
                    0,
                    false,
                    true,
                    $height['inner'],
                    'M'
                );
            }

            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_middle']);

            $bank_name_config = $config['cover_item']['bank_name'];
            $pdf->SetXY($bank_name_config['x'], $bank_name_config['y']);
            $pdf->MultiCell($bank_name_config['width'], 0, $factory->invoice_bank_name);

            $bank_branch_name_config = $config['cover_item']['bank_branch_name'];
            $pdf->SetXY($bank_branch_name_config['x'], $bank_branch_name_config['y']);
            $pdf->MultiCell($bank_branch_name_config['width'], 0, $factory->invoice_bank_branch_name);

            $bank_account_number_config = $config['cover_item']['bank_account_number'];
            $pdf->SetXY($bank_account_number_config['x'], $bank_account_number_config['y']);
            $pdf->MultiCell($bank_account_number_config['width'], 0, $factory->invoice_bank_account_number);

            $bank_account_holder_config = $config['cover_item']['bank_account_holder'];
            $pdf->SetXY($bank_account_holder_config['x'], $bank_account_holder_config['y']);
            $pdf->MultiCell($bank_account_holder_config['width'], 0, $factory->invoice_bank_account_holder);

            $payment_date_config = $config['cover_item']['payment_date'];
            $pdf->SetXY($payment_date_config['x'], $payment_date_config['y']);
            $pdf->MultiCell(
                $payment_date_config['width'],
                0,
                $customer->getPaymentDate($delivery_month)->format($payment_date_config['format']),
                $config['cover_item']['border']
            );

            if (($idx + 1) === $grouped_per_cover->count()) {
                $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_small']);

                $sub_total_config = $config['cover_item']['sub_total'];
                $pdf->SetXY($sub_total_config['x'], $sub_total_config['y']);
                $pdf->MultiCell(
                    $sub_total_config['width'],
                    0,
                    $this->formatNumber($orders->sum('order_amount')),
                    $config['cover_item']['border'],
                    'R'
                );

                $tax_total_config = $config['cover_item']['tax_total'];
                $pdf->SetXY($tax_total_config['x'], $tax_total_config['y']);
                $pdf->MultiCell(
                    $tax_total_config['width'],
                    0,
                    $this->formatNumber($orders->sum('tax_amount')),
                    $config['cover_item']['border'],
                    'R'
                );

                $total_config = $config['cover_item']['total'];
                $pdf->SetXY($total_config['x'], $total_config['y']);
                $pdf->MultiCell(
                    $total_config['width'],
                    0,
                    $this->formatNumber($orders->sum('order_amount_with_tax')),
                    $config['cover_item']['border'],
                    'R'
                );

                $weight_total_config = $config['cover_item']['weight_total'];
                $pdf->SetXY($weight_total_config['x'], $weight_total_config['y']);
                $pdf->MultiCell(
                    $weight_total_config['width'],
                    0,
                    $this->formatWeight($orders->sum('product_weight')),
                    $config['cover_item']['border'],
                    'R'
                );
            }

            $pdf->SetFont($config['font_family'], '', $config['cover_item']['text_middle']);

            $page_config = $config['cover_item']['page'];
            $pdf->Text(
                $page_config['x'],
                $page_config['y'],
                sprintf($page_config['format'], $pdf->getPage(), $number_of_pages),
                false,
                false,
                true,
                0,
                0,
                'C'
            );
        }

        foreach ($grouped_per_detail as $group) {
            foreach ($group->chunked_orders as $idx => $orders) {
                $pdf->AddPage();
                $pdf->setSourceFile($detail_template_path);
                $pdf->useTemplate($pdf->importPage(1), null, null, null, null, true);

                $pdf->SetFont($config['font_family'], '', $config['detail_item']['text_large']);

                $customer_name_config = $config['detail_item']['customer_name'];
                $pdf->SetXY($customer_name_config['x'], $customer_name_config['y']);
                $pdf->MultiCell(
                    $customer_name_config['width'],
                    0,
                    $customer->customer_name.$customer_name_config['suffix'],
                    $config['detail_item']['border'],
                    'L'
                );

                $pdf->SetFont($config['font_family'], '', $config['detail_item']['text_middle']);

                $invoice_date_config = $config['detail_item']['invoice_date'];
                $pdf->SetXY($invoice_date_config['x'], $invoice_date_config['y']);
                $pdf->MultiCell(
                    $invoice_date_config['width'],
                    0,
                    $delivery_month->now()->format($invoice_date_config['format']),
                    $config['detail_item']['border'],
                    'R'
                );

                $delivery_month_config = $config['detail_item']['delivery_month'];
                $pdf->SetXY($delivery_month_config['x'], $delivery_month_config['y']);
                $pdf->MultiCell(
                    $delivery_month_config['width'],
                    0,
                    $delivery_month->format($delivery_month_config['format']),
                    $config['detail_item']['border']
                );

                $end_user_config = $config['detail_item']['end_user'];
                $pdf->SetXY($end_user_config['x'], $end_user_config['y']);
                $pdf->MultiCell(
                    $end_user_config['width'],
                    0,
                    $group->end_user_name.$end_user_config['suffix'],
                    $config['detail_item']['border'],
                    'L'
                );

                $factory_postal_code_config = $config['detail_item']['factory_postal_code'];
                $pdf->SetXY($factory_postal_code_config['x'], $factory_postal_code_config['y']);
                $pdf->MultiCell(
                    $factory_postal_code_config['width'],
                    0,
                    sprintf($factory_postal_code_config['format'], $factory->invoice_postal_code),
                    $config['detail_item']['border'],
                    'L'
                );

                $factory_postal_address_config = $config['detail_item']['factory_address'];
                $pdf->SetXY($factory_postal_address_config['x'], $factory_postal_address_config['y']);
                $pdf->MultiCell(
                    $factory_postal_address_config['width'],
                    0,
                    $factory->invoice_address,
                    $config['detail_item']['border'],
                    'L'
                );

                $factory_phone_number_config = $config['detail_item']['factory_phone_number'];
                $pdf->SetXY($factory_phone_number_config['x'], $factory_phone_number_config['y']);
                $pdf->MultiCell(
                    $factory_phone_number_config['width'],
                    0,
                    sprintf($factory_phone_number_config['format'], $factory->invoice_phone_number),
                    $config['detail_item']['border'],
                    'L'
                );

                $factory_fax_number_config = $config['detail_item']['factory_fax_number'];
                $pdf->SetXY($factory_fax_number_config['x'], $factory_fax_number_config['y']);
                $pdf->MultiCell(
                    $factory_fax_number_config['width'],
                    0,
                    sprintf($factory_fax_number_config['format'], $factory->invoice_fax_number),
                    $config['detail_item']['border'],
                    'L'
                );
/* GGN外す
                $global_gap_number_config = $config['detail_item']['global_gap_number'];
                $pdf->SetXY($global_gap_number_config['x'], $global_gap_number_config['y']);
                $pdf->MultiCell(
                    $global_gap_number_config['width'],
                    0,
                    sprintf($global_gap_number_config['format'], $factory->global_gap_number),
                    $config['detail_item']['border'],
                    'L'
                );
*/
                $pdf->SetFont($config['font_family'], '', $config['detail_item']['text_small']);

                $currency_config = $config['detail_item']['currency'];
                $pdf->SetXY($currency_config['x'], $currency_config['y']);
                $pdf->MultiCell(
                    $currency_config['width'],
                    0,
                    $orders->first()->currency_code,
                    $config['detail_item']['border'],
                    'C'
                );

                $height = $config['detail_item']['order_details']['height'];
                $width = $config['detail_item']['order_details']['width'];
                foreach ($orders->values() as $order_idx => $o) {
                    $col = $config['detail_item']['order_details']['base']['x'];
                    $row = ($order_idx * $height['outer']) + $config['detail_item']['order_details']['base']['y'];

                    $pdf->SetFont($config['font_family'], '', $config['detail_item']['text_middle']);

                    $pdf->MultiCell(
                        $width['delivery_date'],
                        $height['inner'],
                        $o->delivery_date->format('n/j'),
                        $config['detail_item']['border'],
                        'C',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $col += $width['delivery_date'] + $width['border'];
                    $pdf->MultiCell(
                        $width['base_plus_order_number'],
                        $height['inner'],
                        $o->getBasePlusOrderNumber(),
                        $config['detail_item']['border'],
                        'C',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $pdf->SetFont($config['font_family'], '', $config['detail_item']['text_small']);

                    $col += $width['base_plus_order_number'] + $width['border'];
                    $pdf->MultiCell(
                        $width['product_name'],
                        $height['inner'],
                        $o->product_name,
                        $config['detail_item']['border'],
                        'L',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $pdf->SetFont($config['font_family'], '', $config['detail_item']['text_middle']);

                    $col += $width['product_name'] + $width['border'];
                    $pdf->MultiCell(
                        $width['order_quantity'],
                        $height['inner'],
                        $this->formatNumber($o->order_quantity),
                        $config['detail_item']['border'],
                        'R',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $col += $width['order_quantity'] + $width['border'];
                    $pdf->MultiCell(
                        $width['place_order_unit_code'],
                        $height['inner'],
                        $o->place_order_unit_code,
                        $config['detail_item']['border'],
                        'L',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $col += $width['place_order_unit_code'] + $width['border'];
                    $pdf->MultiCell(
                        $width['order_unit'],
                        $height['inner'],
                        $this->formatNumber((float)$o->order_unit),
                        $config['detail_item']['border'],
                        'R',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $col += $width['order_unit'] + $width['border'];
                    $pdf->MultiCell(
                        $width['order_amount'],
                        $height['inner'],
                        $this->formatNumber((float)$o->order_amount),
                        $config['detail_item']['border'],
                        'R',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );

                    $col += $width['order_amount'] + $width['separate_line'] + ($width['border'] * 2);
                    $pdf->MultiCell(
                        $width['order_message'],
                        $height['inner'],
                        $o->order_message,
                        $config['detail_item']['border'],
                        'L',
                        false,
                        1,
                        $col,
                        $row,
                        true,
                        0,
                        false,
                        true,
                        $height['inner'],
                        'M',
                        true
                    );
                }

                if (($idx + 1) === $group->chunked_orders->count()) {
                    $order_amount_config = $config['detail_item']['order_amount'];
                    $pdf->SetXY($order_amount_config['x'], $order_amount_config['y']);
                    $pdf->MultiCell(
                        $order_amount_config['width'],
                        0,
                        $this->formatNumber($group->sum_of_amount),
                        $config['detail_item']['border'],
                        'R'
                    );

                    $tax_config = $config['detail_item']['tax'];
                    $pdf->SetXY($tax_config['x'], $tax_config['y']);
                    $pdf->MultiCell(
                        $tax_config['width'],
                        0,
                        $this->formatNumber($group->sum_of_tax),
                        $config['detail_item']['border'],
                        'R'
                    );

                    $total_config = $config['detail_item']['total'];
                    $pdf->SetXY($total_config['x'], $total_config['y']);
                    $pdf->MultiCell(
                        $total_config['width'],
                        0,
                        $this->formatNumber($group->sum_of_amount_with_tax),
                        $config['detail_item']['border'],
                        'R'
                    );

                    $weight_config = $config['detail_item']['weight'];
                    $pdf->SetXY($weight_config['x'], $weight_config['y']);
                    $pdf->MultiCell(
                        $weight_config['width'],
                        0,
                        $this->formatWeight($group->sum_of_weight),
                        $config['detail_item']['border'],
                        'R'
                    );
                }

                $page_config = $config['detail_item']['page'];
                $pdf->Text(
                    $page_config['x'],
                    $page_config['y'],
                    sprintf($page_config['format'], $pdf->getPage(), $number_of_pages),
                    false,
                    false,
                    true,
                    0,
                    0,
                    'C'
                );
            }
        }

        $this->db->transaction(function () use ($orders) {
            $this->bill_log_repo->create($orders->pluck('order_number')->all());
        });

        return [
            'name' => $config['file_name'].$delivery_month->now()->format('Ymd'),
            'file' => $pdf
        ];
    }

    /**
     * 数値の出力文字列化
     *
     * @param  int|float $number
     * @param  int $decimals
     * @return string
     */
    private function formatNumber($number, int $decimals = 0): string
    {
        return number_format(floatval($number), $decimals);
    }

    /**
     * 重量表示文字列設定
     *
     * @param  float $weight
     * @return string
     */
    private function formatWeight(float $weight): string
    {
        return sprintf(
            config('constant.shipment.invoice.weight_format'),
            $this->formatNumber(convert_to_kilogram($weight), 2)
        );
    }
}
