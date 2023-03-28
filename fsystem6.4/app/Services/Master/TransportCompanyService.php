<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\TransportCompany;
use App\Models\Master\Collections\TransportCompanyCollection;
use App\Repositories\Master\TransportCompanyRepository;

class TransportCompanyService
{
    /**
     * @var \App\Repositories\Master\TransportCompanyRepository
     */
    private $transport_company_repo;

    /**
     * @param  \App\Repositories\Master\TransportCompanyRepository $transport_company_repositry
     * @return void
     */
    public function __construct(TransportCompanyRepository $transport_company_repositry)
    {
        $this->transport_company_repo = $transport_company_repositry;
    }

    /**
     * すべての運送会社マスタを取得
     *
     * @return \App\Models\Master\Collections\TransportCompanyCollection
     */
    public function getAllTransportCompanies(): TransportCompanyCollection
    {
        return $this->transport_company_repo->all();
    }

    /**
     * 運送会社マスタを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchTransportCompanies(array $params, int $page): LengthAwarePaginator
    {
        $transport_companies = $this->transport_company_repo->search($params);
        if ($page > $transport_companies->lastPage() && $transport_companies->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $transport_companies;
    }

    /**
     * 運送会社マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\TransportCompany
     */
    public function createTransportCompany(array $params): TransportCompany
    {
        if (! array_key_exists('can_transport_double', $params)) {
            $params['can_transport_double'] = false;
        }

        return $this->transport_company_repo->create($params);
    }

    /**
     * 運送会社マスタの更新
     *
     * @param  \App\Models\Master\TransportCompany $customer
     * @param  array $params
     * @return \App\Models\Master\TransportCompany
     */
    public function updateTransportCompany(TransportCompany $transport_company, array $params): TransportCompany
    {
        if (! array_key_exists('can_transport_double', $params)) {
            $params['can_transport_double'] = false;
        }

        return $this->transport_company_repo->update($transport_company, $params);
    }

    /**
     * 運送会社マスタの削除
     *
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @return void
     */
    public function deleteTransportCompany(TransportCompany $transport_company): void
    {
        $transport_company->delete();
    }
}
