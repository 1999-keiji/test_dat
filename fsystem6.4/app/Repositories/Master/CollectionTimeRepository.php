<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\CollectionTime;
use App\Models\Master\Collections\CollectionTimeCollection;

class CollectionTimeRepository
{
    /**
     * @var \App\Models\Master\CollectionTime
     */
    private $model;

    /**
     * @param  \App\Models\Master\CollectionTime
     * @return void
     */
    public function __construct(CollectionTime $model)
    {
        $this->model = $model;
    }

    /**
     * 指定された運送会社の集荷時間を検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\CollectionTimeCollection
     */
    public function getCollectionTimesByTransportCompany($params)
    {
        return $this->model
            ->select([
                'transport_company_code',
                'sequence_number',
                'collection_time',
                'remark'
            ])
            ->where('transport_company_code', $params['transport_company_code'])
            ->orderBy('sequence_number', 'ASC')
            ->get();
    }

    /**
     * 集荷時間マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\CollectionTime
     */
    public function create(array $params): CollectionTime
    {
        return $this->model->create($params);
    }

    /**
     * 集荷時間マスタの更新
     *
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @param  array $params
     * @return \App\Models\Master\CollectionTime
     */
    public function update(CollectionTime $collection_time, array $params): CollectionTime
    {
        $collection_time->fill($params)->save();
        return $collection_time;
    }
}
