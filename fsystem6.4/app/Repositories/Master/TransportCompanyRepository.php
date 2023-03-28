<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\TransportCompany;
use App\Models\Master\Collections\TransportCompanyCollection;

class TransportCompanyRepository
{
    /**
     * @var \App\Models\Master\TransportCompany
     */
    private $model;

    /**
     * @param  \App\Models\Master\TransportCompany $model
     * @return void
     */
    public function __construct(TransportCompany $model)
    {
        $this->model = $model;
    }

    /**
     * すべての運送会社マスタを取得
     *
     * @return \App\Models\Master\Collections\TransportCompanyCollection
     */
    public function all(): TransportCompanyCollection
    {
        return $this->model
            ->select([
                'transport_company_code',
                'transport_company_name',
                'transport_branch_name',
                'transport_company_abbreviation',
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
                'note',
                'remark',

            ])
            ->orderBy('transport_company_code', 'ASC')
            ->get();
    }

    /**
     * 運送会社マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($params): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'transport_company_code',
                'transport_company_name',
                'transport_branch_name',
                'transport_company_abbreviation'
            ])
            ->where(function ($query) use ($params) {
                if ($transport_company_name = $params['transport_company_name'] ?? null) {
                    $query->where('transport_company_name', 'LIKE', "%{$transport_company_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($transport_branch_name = $params['transport_branch_name'] ?? null) {
                    $query->where('transport_branch_name', 'LIKE', "%{$transport_branch_name}%");
                }
            })
            ->sortable(['transport_company_code' => 'ASC'])
            ->paginate();
    }

    /**
     * 運送会社マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\TransportCompany
     */
    public function create(array $params): TransportCompany
    {
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 運送会社マスタの更新
     *
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  array $params
     * @return \App\Models\Master\TransportCompany
     */
    public function update(TransportCompany $transport_company, array $params): TransportCompany
    {
        $transport_company->fill(array_filter($params, 'is_not_null'))->save();
        return $transport_company;
    }
}
