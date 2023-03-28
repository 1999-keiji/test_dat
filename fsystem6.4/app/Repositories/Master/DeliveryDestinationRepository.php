<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\Collections\DeliveryDestinationCollection;

class DeliveryDestinationRepository
{
    /**
     * @var \App\Models\Master\DeliveryDestination
     */
    private $model;

    /**
     * @param  \App\Models\Master\DeliveryDestination $model
     * @return void
     */
    public function __construct(DeliveryDestination $model)
    {
        $this->model = $model;
    }

    /**
     * 納入先マスタの取得
     *
     * @param  string $delivery_destination_code
     * @return \App\Models\Master\DeliveryDestination
     */
    public function find(string $delivery_destination_code): DeliveryDestination
    {
        return $this->model->find($delivery_destination_code);
    }

    /**
     * 納入先マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($params): LengthAwarePaginator
    {
        return $this->model
            ->select([
                'delivery_destination_code',
                'delivery_destination_name',
                'creating_type'
            ])
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code'] ?? null) {
                    $query->where('delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_name = $params['delivery_destination_name'] ?? null) {
                    $query->where('delivery_destination_name', 'LIKE', "%{$delivery_destination_name}%")
                        ->orWhere('delivery_destination_name2', 'LIKE', "%{$delivery_destination_name}%")
                        ->orWhere('delivery_destination_abbreviation', 'LIKE', "%{$delivery_destination_name}%");
                }
            })
            ->sortable(['delivery_destination_code' => 'ASC'])
            ->paginate();
    }

    /**
     * 一意納入先の検索
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryDestination|null
     */
    public function searchPrimary($delivery_destination_code)
    {
        return $this->model
            ->where('delivery_destination_code', $delivery_destination_code)
            ->first();
    }

    /**
     * API用に納入先マスタを条件に応じて検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\DeliveryDestinationCollection
     */
    public function searchForSearchingApi($params): DeliveryDestinationCollection
    {
        $query = $this->model
            ->select([
                'delivery_destination_code',
                'delivery_destination_abbreviation',
                'address',
                'phone_number',
                'end_user_code'
            ])
            ->where(function ($query) use ($params) {
                if ($delivery_destination_code = $params['delivery_destination_code']) {
                    $query->where('delivery_destination_code', $delivery_destination_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_name = $params['delivery_destination_name']) {
                    $query->where('delivery_destination_name', 'LIKE', "%{$delivery_destination_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_name2 = $params['delivery_destination_name2']) {
                    $query->where('delivery_destination_name2', 'LIKE', "%{$delivery_destination_name2}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_abbreviation = $params['delivery_destination_abbreviation']) {
                    $query->where(
                        'delivery_destination_abbreviation',
                        'LIKE',
                        "%{$delivery_destination_abbreviation}%"
                    );
                }
            })
            ->where(function ($query) use ($params) {
                if ($delivery_destination_name_kana = $params['delivery_destination_name_kana']) {
                    $query->where('delivery_destination_name_kana', 'LIKE', "%{$delivery_destination_name_kana}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($address = $params['address']) {
                    $query->where('address', 'LIKE', "%{$address}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($phone_number = $params['phone_number']) {
                    $query->where('phone_number', "%{$phone_number}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code']) {
                    $query->whereIn('delivery_destination_code', function ($query) use ($factory_code) {
                        $query->select('delivery_destination_code')
                            ->from('delivery_factory_products')
                            ->where('factory_code', $factory_code);
                    });
                }
            })
            ->whereNotNull('delivery_destinations.end_user_code')
            ->where('delivery_destinations.can_display', true)
            ->orderBy('delivery_destinations.delivery_destination_code', 'ASC');

        if ($params['limited']) {
            $query = $query->limit(DeliveryDestination::API_SEARCHING_LIMIT);
        }

        return $query->get();
    }

    /**
     * 納入先マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\DeliveryDestination
     */
    public function create(array $params): DeliveryDestination
    {
        unset($params['end_user_name'], $params['selected_master']);
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * 納入先マスタの更新
     *
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @param  array $params
     * @return \App\Models\Master\DeliveryDestination $delivery_destination
     */
    public function update(DeliveryDestination $delivery_destination, array $params): DeliveryDestination
    {
        unset($params['end_user_name'], $params['selected_master']);

        $delivery_destination->fill(array_filter($params, 'is_not_null'))->save();
        return $delivery_destination;
    }
}
