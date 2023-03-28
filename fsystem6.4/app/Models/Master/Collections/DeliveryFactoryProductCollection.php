<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Order\Collections\OrderCollection;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Integer\DeliveryLeadTime;

class DeliveryFactoryProductCollection extends Collection
{
    /**
     * 工場コードで絞り込み
     *
     * @param  string $factory_code
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function filterByFactory(string $factory_code): DeliveryFactoryProductCollection
    {
        return $this->where('factory_code', $factory_code);
    }

    /**
     * 紐づく工場商品の商品コードを抽出
     *
     * @return array
     */
    public function pluckProductCode(): array
    {
        return $this
            ->map(function ($dfp) {
                return $dfp->factory_product->product_code;
            })
            ->all();
    }

    /**
     * 受注実績のある納入工場商品を抽出
     *
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @return \App\Models\Master\Collections\DeliveryFactoryProductCollection
     */
    public function filterByOrders(OrderCollection $orders): DeliveryFactoryProductCollection
    {
        return $this
            ->filter(function ($dfp) use ($orders) {
                return
                    in_array(
                        $dfp->delivery_destination_code,
                        $orders->pluck('delivery_destination_code')->all(),
                        true
                    ) &&
                    in_array(
                        $dfp->factory_product_sequence_number,
                        $orders->pluck('factory_product_sequence_number')->all(),
                        true
                    );
            });
    }

    /**
     * 工場取扱商品ごとにグルーピング
     *
     * @return array
     */
    public function groupByFactoryProduct(): array
    {
        return $this
            ->groupBy(function ($dfp) {
                return implode('|', [
                    $dfp->factory_code,
                    $dfp->factory_product_sequence_number
                ]);
            })
            ->map(function ($group, $factory_product) {
                [$factory_code, $factory_product_sequence_number] = explode('|', $factory_product);
                return (object)[
                    'factory_code' => $factory_code,
                    'factory_product_sequence_number' => (int)$factory_product_sequence_number,
                    'factory_product_abbreviation' => $group->first()->factory_product_abbreviation,
                    'weight_per_number_of_heads' => $group->first()->weight_per_number_of_heads,
                    'number_of_cases' => $group->first()->number_of_cases,
                    'delivery_destinations' => $group
                        ->map(function ($dfp) {
                            $latest_delivery_date = null;
                            if (! is_null($dfp->latest_delivery_date)) {
                                $latest_delivery_date = DeliveryDate::parse($dfp->latest_delivery_date);
                            }

                            return (object)[
                                'delivery_destination_code' => $dfp->delivery_destination_code,
                                'delivery_destination_abbreviation' => $dfp->delivery_destination_abbreviation,
                                'delivery_lead_time' => $dfp->delivery_lead_time,
                                'shipment_lead_time' => $dfp->shipment_lead_time,
                                'latest_delivery_date' => $latest_delivery_date
                            ];
                        })
                        ->all()
                ];
            })
            ->values()
            ->all();
    }

    /**
     * 商品規格ごとにグルーピング
     *
     * @return array
     */
    public function groupByPackagingStyle(): array
    {
        return $this
            ->groupBy(function ($dfp) {
                return implode('|', [
                    $dfp->number_of_heads,
                    $dfp->weight_per_number_of_heads,
                    $dfp->input_group
                ]);
            })
            ->map(function ($group, $packaging_style) {
                [$number_of_heads, $weight_per_number_of_heads, $input_group] = explode('|', $packaging_style);

                $list_of_number_of_cases = $group->groupBy('number_of_cases')
                    ->map(function ($group, $number_of_cases) {
                        $delivery_destinations = $group->map(function ($dd) {
                            $delivery_lead_time = $dd->delivery_lead_time;
                            if (! is_null($delivery_lead_time)) {
                                $delivery_lead_time = new DeliveryLeadTime($delivery_lead_time);
                            }
                            if (is_null($delivery_lead_time)) {
                                $delivery_lead_time = (new DeliveryLeadTime)->getDefaultDeliveryLeadTime();
                            }

                            $latest_delivery_date = null;
                            if (! is_null($dd->latest_delivery_date)) {
                                $latest_delivery_date = DeliveryDate::parse($dd->latest_delivery_date);
                            }

                            return (object)[
                                'delivery_destination_code' => $dd->delivery_destination_code,
                                'delivery_destination_abbreviation' => $dd->delivery_destination_abbreviation,
                                'factory_code' => $dd->factory_code,
                                'factory_product_sequence_number' => $dd->factory_product_sequence_number,
                                'transport_company_abbreviation' => $dd->transport_company_abbreviation,
                                'collection_time' => $dd->collection_time,
                                'delivery_lead_time' => $delivery_lead_time,
                                'note' => $dd->note,
                                'latest_delivery_date' => $latest_delivery_date
                            ];
                        })
                            ->all();

                        return (object)compact('number_of_cases', 'delivery_destinations');
                    })
                    ->values()
                    ->all();

                return (object)[
                    'number_of_heads' => $number_of_heads,
                    'weight_per_number_of_heads' => $weight_per_number_of_heads,
                    'input_group' => $input_group,
                    'count_of_delivery_destination' => $group->count(),
                    'list_of_number_of_cases' => $list_of_number_of_cases
                ];
            })
            ->values()
            ->all();
    }
}
