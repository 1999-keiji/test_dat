<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Customer;
use App\Models\Master\Collections\CustomerCollection;
use App\ValueObjects\Enum\CanDisplay;

class CustomerRepository
{
    /**
     * @var \App\Models\Master\Customer
     */
    private $model;

    /**
     * @param  \App\Models\Master\Customer $model
     * @return void
     */
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    /**
     * すべての得意先マスタを取得
     *
     * @return \App\Models\Master\Collections\CustomerCollection
     */
    public function all(): CustomerCollection
    {
        return $this->model
            ->select([
                'customer_code',
                'is_default_customer',
                'customer_name',
                'customer_name2',
                'customer_abbreviation',
                'customer_name_kana',
                'customer_name_english',
                'country_code',
                'postal_code',
                'prefecture_code',
                'address',
                'address2',
                'address3',
                'abroad_address',
                'abroad_address2',
                'abroad_address3',
                'phone_number',
                'extension_number',
                'fax_number',
                'mail_address',
                'closing_date',
                'payment_timing_month',
                'payment_timing_date',
                'basis_for_recording_sales',
                'rounding_type',
                'can_display',
                'remark'
            ])
            ->where('can_display', CanDisplay::CAN_DISPLAY)
            ->orderBy('customer_code', 'ASC')
            ->get();
    }

    /**
     * 得意先マスタを取得
     *
     * @param  string $factory_code
     * @return \App\Models\Master\Factory
     */
    public function find(string $customer_code): Customer
    {
        return $this->model->find($customer_code);
    }

    /**
     * 得意先マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($params): LengthAwarePaginator
    {
        return $this->model->select([
            'customer_code',
            'customer_name',
            'is_default_customer'
        ])
            ->where(function ($query) use ($params) {
                if (array_key_exists('customer_code', $params) && $customer_code = $params['customer_code']) {
                    $query->where('customer_code', $customer_code);
                }
            })
            ->where(function ($query) use ($params) {
                if (array_key_exists('customer_name', $params) && $customer_name = $params['customer_name']) {
                    $query->where('customer_name', 'LIKE', "%{$customer_name}%")
                        ->orWhere('customer_name2', 'LIKE', "%{$customer_name}%")
                        ->orWhere('customer_abbreviation', 'LIKE', "%{$customer_name}%");
                }
            })
            ->sortable(['customer_code' => 'ASC'])
            ->with('end_users')
            ->paginate();
    }

    /**
     * デフォルト得意先を検索
     *
     * @return \App\Models\Master\Customer
     */
    public function searchDefaultCustomer()
    {
        return $this->model
            ->select(['customer_code'])
            ->where('is_default_customer', true)
            ->first();
    }

    /**
     * 得意先マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Customer
     */
    public function create(array $params): Customer
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 得意先マスタの更新
     *
     * @param  \App\Models\Master\Customer $customer
     * @param  array $params
     * @return \App\Models\Master\Customer
     */
    public function update(Customer $customer, array $params): Customer
    {
        $customer->fill(array_filter($params, 'is_not_null'))->save();
        return $customer;
    }
}
