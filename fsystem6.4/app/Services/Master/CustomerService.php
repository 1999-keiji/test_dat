<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\Collections\CustomerCollection;
use App\Repositories\Master\CustomerRepository;
use App\Models\Master\Customer;

class CustomerService
{
    /**
     * @var \App\Repositories\Master\CustomerRepository
     */
    private $customer_repo;

    /**
     * @param  \App\Repositories\Master\CustomerRepository $customer_repositry
     * @return void
     */
    public function __construct(CustomerRepository $customer_repositry)
    {
        $this->customer_repo = $customer_repositry;
    }

    /**
     * 得意先マスタを取得
     *
     * @param  string $factory_code
     * @return \App\Models\Master\Factory
     */
    public function find(string $customer_code): Customer
    {
        return $this->customer_repo->find($customer_code);
    }

    /**
     * すべての得意先マスタを取得
     *
     * @return \App\Models\Master\Collections\CustomerCollection
     */
    public function getAllCustomers(): CustomerCollection
    {
        return $this->customer_repo->all();
    }

    /**
     * 得意先マスタを条件に応じて検索
     *
     * @param  array $params 検索条件
     * @param  int $page 表示ページ
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchCustomers(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'customer_code' => $params['customer_code'] ?? null,
            'customer_name' => $params['customer_name'] ?? null
        ];

        $customers = $this->customer_repo->search($params);
        if ($page > $customers->lastPage() && $customers->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $customers;
    }

    /**
     * 得意先マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Customer
     */
    public function createCustomer(array $params): Customer
    {
        return $this->customer_repo->create($params);
    }

    /**
     * 得意先マスタの更新
     *
     * @param  \App\Models\Master\Customer $customer
     * @param  array $params
     * @return \App\Models\Master\Customer
     */
    public function updateCustomer(Customer $customer, array $params): Customer
    {
        return $this->customer_repo->update($customer, $params);
    }

    /**
     * 得意先マスタの削除
     *
     * @param  \App\Models\Master\Customer $customer
     * @return void
     */
    public function deleteCustomer(Customer $customer): void
    {
        $customer->delete();
    }
}
