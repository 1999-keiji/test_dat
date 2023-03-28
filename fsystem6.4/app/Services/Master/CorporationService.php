<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\PageOverException;
use App\Models\Master\Corporation;
use App\Models\Master\Collections\CorporationCollection;
use App\Repositories\Master\CorporationRepository;

class CorporationService
{
    /**
     * @var \App\Repositories\Master\CorporationRepository
     */
    private $corporation_repo;

    /**
     * @param  \App\Repositories\Master\CorporationRepository $corporation_repositry
     * @return void
     */
    public function __construct(CorporationRepository $corporation_repositry)
    {
        $this->corporation_repo = $corporation_repositry;
    }

    /**
     * 法人マスタを条件に応じて検索
     *
     * @param  array $params
     * @param  int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchCorporations(array $params, int $page): LengthAwarePaginator
    {
        $corporations = $this->corporation_repo->search($params);
        if ($page > $corporations->lastPage() && $corporations->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $corporations;
    }

    /**
     * 法人マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Corporation
     */
    public function createCorporation(array $params): Corporation
    {
        return $this->corporation_repo->create($params);
    }

    /**
     * 法人マスタの更新
     *
     * @param  \App\Models\Master\Corporation $corporation
     * @param  array $params
     * @return \App\Models\Master\Corporation $corporations
     */
    public function updateCorporation(Corporation $corporation, array $params): Corporation
    {
        return $this->corporation_repo->update($corporation, $params);
    }

    /**
     * 法人マスタの削除
     *
     * @param  \App\Models\Master\Corporation $corporation
     * @return void
     */
    public function deleteCorporation(Corporation $corporation): void
    {
        $corporation->delete();
    }

    /**
     * すべての法人マスタを取得
     *
     * @return \App\Models\Master\Collections\CorporationCollection
     */
    public function getAllCorporations(): CorporationCollection
    {
        return $this->corporation_repo->all();
    }
}
