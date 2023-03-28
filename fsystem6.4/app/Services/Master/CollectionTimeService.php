<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use App\Models\Master\CollectionTime;
use App\Models\Master\TransportCompany;
use App\Repositories\Master\CollectionTimeRepository;
use App\Models\Master\Collections\CollectionTimeCollection;

class CollectionTimeService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\CollectionTimeRepository
     */
    private $collection_time_repo;

    /**
     * @param  \Illuminate\Database\Connection
     * @param  \App\Repositories\Master\CollectionTimeRepository $delivery_warehouse_repo
     * @return void
     */
    public function __construct(Connection $db, CollectionTimeRepository $collection_time_repo)
    {
        $this->db = $db;
        $this->collection_time_repo = $collection_time_repo;
    }

    /**
     * 集荷時間マスタの登録
     *
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  array $params
     * @return \App\Models\Master\CollectionTime
     */
    public function createCollectionTime(TransportCompany $transport_company, array $params): CollectionTime
    {
        return $this->db->transaction(function () use ($transport_company, $params) {
            $current_sequence_number = $transport_company->collection_times->max('sequence_number') ?: 0;
            return $this->collection_time_repo->create([
                'transport_company_code' => $transport_company->transport_company_code,
                'sequence_number' => $current_sequence_number + 1,
                'collection_time' => $params['collection_time'],
                'remark' => $params['remark'] ?: ''
            ]);
        });
    }

    /**
     * 集荷時間マスタの更新
     *
     * @param  \App\Models\Master\CollectionTime $delivery_warehouse
     * @param  array $params
     * @return \App\Models\Master\CollectionTime $delivery_warehouse
     */
    public function updateCollectionTime(CollectionTime $collection_time, array $params): CollectionTime
    {
        return $this->collection_time_repo->update($collection_time, [
            'collection_time' => $params['collection_time'],
            'remark' => $params['remark'] ?: ''
        ]);
    }

    /**
     * 集荷時間マスタの削除
     *
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @return void
     */
    public function deleteCollectionTime(CollectionTime $collection_time): void
    {
        $collection_time->delete();
    }

    /**
     * API用 指定された運送会社の集荷時間を検索
     *
     * @param  array $params
     * @return array
     */
    public function getCollectionTimesByTransportCompanyForApi(array $params): array
    {
        $params = [
            'transport_company_code' => $params['transport_company_code'] ?? null
        ];

        return $this->collection_time_repo->getCollectionTimesByTransportCompany($params)->toResponseForSearchingApi();
    }
}
