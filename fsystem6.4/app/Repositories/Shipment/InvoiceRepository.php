<?php

declare(strict_types=1);

namespace App\Repositories\Shipment;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use App\Models\Master\Customer;
use App\Models\Shipment\Invoice;
use App\Models\Shipment\Collections\InvoiceCollection;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Enum\ClosingDate;
use App\ValueObjects\Enum\ProcessClass;
use App\ValueObjects\Enum\SlipType;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\String\InvoiceNumber;

class InvoiceRepository
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Shipment\Invoice
     */
    private $model;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Shipment\Invoice
     * @return void
     */
    public function __construct(AuthManager $auth, Connection $db, Invoice $model)
    {
        $this->auth = $auth;
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 請求書データの検索
     *
     * @param  array $params
     * @param  \App\Models\Master\Customer $customer
     * @param  \App\ValueObjects\Date\DeliveryDate|null $delivery_month
     * @param  array $order
     * @return \App\Models\Shipment\Collections\InvoiceCollection
     */
    public function searchInvoices(
        array $params,
        Customer $customer,
        ?DeliveryDate $delivery_month,
        array $order
    ): InvoiceCollection {
        $tax_query = 'SELECT tax.application_started_on, '.
            "COALESCE(DATE_SUB(sub_tax.application_started_on, INTERVAL 1 DAY), '2099-12-31') ".
            'AS application_ended_on, tax.tax_rate '.
            'FROM tax '.
            'LEFT JOIN tax sub_tax ON sub_tax.application_started_on = ('.
            'SELECT MIN(application_started_on) AS application_started_on '.
            'FROM tax sub_tax2 '.
            'WHERE tax.application_started_on < sub_tax2.application_started_on)';

        $interval_month = sprintf('INTERVAL %d month', $customer->closing_date === ClosingDate::END_OF_MONTH ? 0 : 1);
        $interval_day = sprintf(
            'INTERVAL %d day',
            $customer->closing_date === ClosingDate::END_OF_MONTH ? 0 : $customer->closing_date
        );

        $latest_delivery_month = $customer->getLatestDeliveryMonthByFactory($params['factory_code']);
        $latest_delivery_date = $customer->getEndOfDeliveryDateOfInvoice($latest_delivery_month)->format('Y-m-d');
        $next_delivery_month = $latest_delivery_month->addMonth()->firstOfMonth()->format('Y/m');

        $raw = '(CASE WHEN invoices.delivery_month IS NULL THEN ('.
            "CASE WHEN orders.delivery_date > \"{$latest_delivery_date}\" THEN ".
            "(DATE_FORMAT(DATE_ADD(DATE_SUB(orders.delivery_date, {$interval_day}), ".
            "{$interval_month}), '%Y/%m')) ELSE \"{$next_delivery_month}\" END) ELSE invoices.delivery_month END)";

        $order_query = $this->db->table('orders')
            ->select([
                'orders.factory_code',
                'orders.customer_code',
                $this->db->raw("{$raw} AS delivery_month"),
                $this->db->raw('COUNT(orders.order_number) AS order_quantity'),
                $this->db->raw(
                    'SUM(orders.order_amount - '.
                    'COALESCE((returned_products.unit_price * returned_products.quantity), 0) + '.
                    sprintf(
                        '%s((orders.order_amount - '.
                        'COALESCE((returned_products.unit_price * returned_products.quantity), 0)) * taxes.tax_rate)',
                        $customer->getRoundingSql()
                    ).
                    ') AS order_amount'
                )
            ])
            ->join(
                'delivery_destinations',
                'orders.delivery_destination_code',
                '=',
                'delivery_destinations.delivery_destination_code'
            )
            ->join($this->db->raw("({$tax_query}) AS taxes"), function ($join) {
                $join->on('taxes.application_started_on', '<=', 'orders.shipping_date')
                    ->on('taxes.application_ended_on', '>=', 'orders.shipping_date');
            })
            ->leftJoin('returned_products', 'returned_products.order_number', '=', 'orders.order_number')
            ->leftJoin('invoices', function ($join) {
                $join->on('invoices.invoice_number', '=', 'orders.invoice_number')
                    ->where('invoices.has_fixed', true);
            })
            ->where('orders.factory_code', $params['factory_code'])
            ->where('orders.customer_code', $params['customer_code'])
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('orders.process_class', '<>', ProcessClass::CANCEL_PROCESS)
            ->where('orders.slip_status_type', '<>', SlipStatusType::RELATION_TEMP_ORDER)
            ->where('orders.factory_cancel_flag', false)
            ->whereNotNull('orders.fixed_shipping_at')
            ->groupBy(
                'orders.factory_code',
                'orders.customer_code',
                $this->db->raw($raw)
            );

        $query = $this->model
            ->select([
                'invoices.invoice_number',
                'orders.factory_code',
                'orders.customer_code',
                'orders.delivery_month',
                'orders.order_quantity AS orders_order_quantity',
                'invoices.order_quantity',
                'orders.order_amount AS orders_order_amount',
                'invoices.order_amount',
                'users.user_name AS fix_user_name',
                $this->db->raw("DATE_FORMAT(invoices.fixed_at, '%Y/%m/%d %H:%i:%s') AS fixed_at"),
                'invoices.has_fixed'
            ])
            ->rightJoin($this->db->raw("({$order_query->toSql()}) AS orders"), function ($join) {
                $join->on('orders.factory_code', '=', 'invoices.factory_code')
                    ->on('orders.customer_code', '=', 'invoices.customer_code')
                    ->on('orders.delivery_month', '=', 'invoices.delivery_month');
            })
            ->setBindings($order_query->getBindings())
            ->leftJoin('users', 'users.user_code', '=', 'invoices.fixed_by')
            ->where(function ($query) use ($customer, $delivery_month) {
                if (! is_null($delivery_month)) {
                    $query->where('orders.delivery_month', $delivery_month->format('Y/m'));
                }
            })
            ->where(function ($query) use ($params) {
                $query->whereNull('invoices.invoice_number')
                    ->orWhereIn('invoices.invoice_number', function ($query) use ($params) {
                        $query->select($this->db->raw('MAX(invoice_number)'))
                            ->from('invoices')
                            ->where('orders.factory_code', $params['factory_code'])
                            ->where('orders.customer_code', $params['customer_code'])
                            ->groupBy('factory_code', 'customer_code', 'delivery_month');
                    });
            });

        if (count($order) === 0) {
            $query->orderBy('orders.delivery_month', 'DESC');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->get();
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
        return $this->model->select('invoice_number')
            ->where('factory_code', $params['factory_code'])
            ->where('customer_code', $params['customer_code'])
            ->where('delivery_month', $delivery_month->format('Y/m'))
            ->where('has_fixed', true)
            ->first();
    }

    /**
     * 請求書データの登録
     *
     * @param  array $params
     * @return \App\Models\Shipment\Invoice
     */
    public function createInvoice(array $params): Invoice
    {
        $count = $this->model
            ->where('factory_code', $params['factory_code'])
            ->where('delivery_month', $params['delivery_month'])
            ->count() + 1;

        $invoice_number =
            InvoiceNumber::generateInvoiceNumber(
                $params['factory_code'],
                DeliveryDate::createFromYearMonth($params['delivery_month']),
                $count
            );

        return $this->model->create([
            'invoice_number' => $invoice_number,
            'factory_code' => $params['factory_code'],
            'customer_code' => $params['customer_code'],
            'delivery_month' => $params['delivery_month'],
            'fixed_by' => $this->auth->user()->user_code,
            'fixed_at' => Chronos::now()->format('Y-m-d H:i:s')
        ]);
    }
}
