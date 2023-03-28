<?php

declare(strict_types=1);

namespace App\Models\Order\Collections;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use App\Models\Master\Warehouse;
use App\Models\Master\Collections\EndUserCollection;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Enum\ShipmentLeadTime;
use App\ValueObjects\Enum\SlipStatusType;
use App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass;
use App\ValueObjects\Integer\DeliveryLeadTime;

class OrderCollection extends Collection
{
    /**
     * 注文データと受注フォーキャストデータから、工場の情報を抽出
     *
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function pluckFactories(OrderForecastCollection $order_forecasts): OrderCollection
    {
        $factories = $order_forecasts->groupByFactory()
            ->reduce(function ($factories, $f) {
                if (! $factories->pluck('factory_code')->containsStrict($f->factory_code)) {
                    $factories->push($f);
                }

                return $factories;
            }, $this->groupByFactory())
            ->map(function ($f) {
                unset($f->orders, $f->order_forecasts);
                return $f;
            })
            ->all();

        return new self($factories);
    }

    /**
     * 注文データと受注フォーキャストデータから、工場商品の情報を抽出
     *
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function pluckFactoryProducts(OrderForecastCollection $order_forecasts): OrderCollection
    {
        $factories = $this->groupBy('factory_code')
            ->reduce(function ($factories, $grouped) {
                $factory = (object)[
                    'factory_code' => $grouped->first()->factory_code,
                    'factory_abbreviation' => $grouped->first()->factory_abbreviation,
                    'factory_products' => $grouped->groupByFactoryProduct()->map(function ($fp) {
                        unset($fp->orders);
                        return $fp;
                    })
                ];

                $factories->push($factory);
                return $factories;
            }, new self([]));

        return $order_forecasts->groupBy('factory_code')
            ->reduce(function ($factories, $grouped) {
                $factory = (object)[
                    'factory_code' => $grouped->first()->factory_code,
                    'factory_abbreviation' => $grouped->first()->factory_abbreviation,
                    'factory_products' => $grouped->groupByFactoryProduct()->map(function ($fp) {
                        unset($fp->order_forecasts);
                        return $fp;
                    })
                ];

                if (! $factories->pluck('factory_code')->containsStrict($factory->factory_code)) {
                    $factories->push($factory);
                    return $factories;
                }

                $idx = $factories->search(function ($f) use ($factory) {
                    return $f->factory_code === $factory->factory_code;
                });

                $duplicted = $factories[$idx];
                $duplicted->factory_products = $factory->factory_products->reduce(function ($factory_products, $fp) {
                    if (! $factory_products
                        ->pluck('factory_product_sequence_number')->containsStrict($fp->factory_product_sequence_number)
                    ) {
                        $factory_products->push($fp);
                    }

                    return $factory_products;
                }, $duplicted->factory_products);

                $factories[$idx] = $duplicted;
                return $factories;
            }, $factories);
    }

    /**
     * 注文データと受注フォーキャストデータから、納入先の情報を抽出
     *
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function pluckDeliveryDestinations(OrderForecastCollection $order_forecasts): OrderCollection
    {
        $delivery_destinations = $order_forecasts->groupByDeliveryDestination()
            ->reduce(function ($delivery_destinations, $dd) {
                if (! $delivery_destinations
                    ->pluck('delivery_destination_code')
                    ->containsStrict($dd->delivery_destination_code)) {
                    $delivery_destinations->push($dd);
                }

                return $delivery_destinations;
            }, $this->groupByDeliveryDestination())
            ->map(function ($dd) {
                unset($dd->orders, $dd->order_forecasts);
                return $dd;
            })
            ->all();

        return new self($delivery_destinations);
    }

    /**
     * 注文データと受注フォーキャストデータから、商品規格の情報を抽出
     *
     * @param  \App\Models\Order\Collections\OrderForecastCollection $order_forecasts
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function pluckPackagingStyles(OrderForecastCollection $order_forecasts): OrderCollection
    {
        $factories = $this->groupBy('factory_code')
            ->reduce(function ($factories, $grouped) {
                $factory = (object)[
                    'factory_code' => $grouped->first()->factory_code,
                    'factory_abbreviation' => $grouped->first()->factory_abbreviation,
                    'packaging_styles' => $grouped->groupByPackagingStyle()->map(function ($ps) {
                        unset($ps->orders);
                        return $ps;
                    })
                ];

                $factories->push($factory);
                return $factories;
            }, new self([]));

        return $order_forecasts->groupBy('factory_code')
            ->reduce(function ($factories, $grouped) {
                $factory = (object)[
                    'factory_code' => $grouped->first()->factory_code,
                    'factory_abbreviation' => $grouped->first()->factory_abbreviation,
                    'packaging_styles' => $grouped->groupByPackagingStyle()->map(function ($ps) {
                        unset($ps->order_forecasts);
                        return $ps;
                    })
                ];

                if (! $factories->pluck('factory_code')->containsStrict($factory->factory_code)) {
                    $factories->push($factory);
                    return $factories;
                }

                $idx = $factories->search(function ($f) use ($factory) {
                    return $f->factory_code === $factory->factory_code;
                });

                $duplicted = $factories[$idx];
                $duplicted->packaging_styles = $factory->packaging_styles->reduce(function ($packaging_styles, $ps) {
                    if (! $packaging_styles
                        ->pluck('packaging_style_key')->containsStrict($ps->packaging_style_key)
                    ) {
                        $packaging_styles->push($ps);
                    }

                    return $packaging_styles;
                }, $duplicted->packaging_styles);

                $factories[$idx] = $duplicted;
                return $factories;
            }, $factories);
    }

    /**
     * 工場を条件に抽出
     *
     * @param  \stdClass $factory
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByFactory(stdClass $factory): OrderCollection
    {
        return $this->where('factory_code', $factory->factory_code);
    }

    /**
     * 工場商品を条件に抽出
     *
     * @param  \stdClass $factory_product
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByFactoryProduct(stdClass $factory_product): OrderCollection
    {
        return $this->filter(function ($o) use ($factory_product) {
            return $o->factory_code === $factory_product->factory_code &&
                $o->factory_product_sequence_number === $factory_product->factory_product_sequence_number;
        });
    }

    /**
     * 納入先を条件に抽出
     *
     * @param  \stdClass $delivery_destination
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByDeliveryDestination(stdClass $delivery_destination): OrderCollection
    {
        return $this->where('delivery_destination_code', $delivery_destination->delivery_destination_code);
    }

    /**
     * 商品規格を条件に抽出
     *
     * @param  \stdClass $packaging_style
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByPackagingStyle(stdClass $packaging_style): OrderCollection
    {
        return $this->filter(function ($o) use ($packaging_style) {
            return $o->number_of_heads === $packaging_style->number_of_heads &&
                $o->weight_per_number_of_heads === $packaging_style->weight_per_number_of_heads &&
                $o->input_group === $packaging_style->input_group;
        });
    }

    /**
     * 納入日もしくは納入日を条件に抽出
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Order\Collections\OrderCollection
     * @throws InvalidArgumentException
     */
    public function filterByDate(Date $date): OrderCollection
    {
        if ($date instanceof DeliveryDate) {
            return $this->filterByDeliveryDate($date);
        }
        if ($date instanceof ShippingDate) {
            return $this->filterByShippingDate($date);
        }

        throw new InvalidArgumentException('target date was invalid:'. get_class($date));
    }

    /**
     * 納入日を条件に抽出
     *
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_date
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByDeliveryDate(DeliveryDate $delivery_date): OrderCollection
    {
        return $this->where('delivery_date', $delivery_date->format('Y-m-d'));
    }

    /**
     * 出荷日を条件に抽出
     *
     * @param  \App\ValueObjects\Date\ShippingDate $shipping_date
     * @param  \App\ValueObjects\Enum\ShipmentLeadTime $shipment_lead_time
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByShippingDate(
        ShippingDate $shipping_date,
        ?ShipmentLeadTime $shipment_lead_time = null
    ): OrderCollection {
        if (! is_null($shipment_lead_time) && $shipment_lead_time->willShipOnTheDate()) {
            $shipping_date = $shipping_date->subDay();
        }

        return $this->where('shipping_date', $shipping_date->format('Y-m-d'));
    }

    /**
     * 出荷年月を条件に抽出
     *
     * @param  string $shipping_month
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterByShippingMonth(string $shipping_month): OrderCollection
    {
        return $this->filter(function ($o) use ($shipping_month) {
            return $o->shipping_month === $shipping_month;
        });
    }

    /**
     * 注文数と商品重量の合計、仮注文が含まれるか否かを取得
     *
     * @return array
     */
    public function toSumOfQuantityAndWeight(): array
    {
        return [
            'weight' => convert_to_kilogram($this->pluck('product_weight')->sum()),
            'quantity' => $this->pluck('order_quantity')->sum(),
            'only_fixed_order' => $this->pluck('is_temporary_order')->sum() === 0
        ];
    }

    /**
     * 日付単位の合計を算出
     *
     * @param  mixed $week
     * @param  string $date
     * @param  string $display_unit
     * @return int|float
     */
    public function toSumPerDate($week, string $date, string $display_unit)
    {
        $target = str_plural($display_unit);
        return $this->pluck("orders.{$week}.{$target}.{$date}")->sum();
    }

    /**
     * 日付単位の総合計を算出
     *
     * @param  mixed $week
     * @param  string $date
     * @param  string $display_unit
     * @return int|float
     */
    public function toSumOfWholePerDate($week, string $date, string $display_unit)
    {
        return $this->reduce(function ($sum, $unit) use ($week, $date, $display_unit) {
            if (property_exists($unit, 'factory_products')) {
                return $sum += $unit->factory_products->toSumPerDate($week, $date, $display_unit);
            }
            if (property_exists($unit, 'delivery_destinations')) {
                return $sum += $unit->delivery_destinations->toSumPerDate($week, $date, $display_unit);
            }

            $target = str_plural($display_unit);
            return $sum += $unit->orders[$week][$target][$date];
        }, 0);
    }

    /**
     * 週単位の合計を算出
     *
     * @param  mixed $week
     * @param  string $display_unit
     * @return int|float
     */
    public function toSumPerWeek($week, string $display_unit)
    {
        $target = str_plural($display_unit);
        return $this->pluck("orders.{$week}.{$target}.total")->sum();
    }

    /**
     * 週単位の総合計を算出
     *
     * @param  mixed $week
     * @param  string $display_unit
     * @return int|float
     */
    public function toSumOfWholePerWeek($week, string $display_unit)
    {
        return $this->reduce(function ($sum, $unit) use ($week, $display_unit) {
            if (property_exists($unit, 'factory_products')) {
                return $sum += $unit->factory_products->toSumPerWeek($week, $display_unit);
            }
            if (property_exists($unit, 'delivery_destinations')) {
                return $sum += $unit->delivery_destinations->toSumPerWeek($week, $display_unit);
            }

            $target = str_plural($display_unit);
            return $sum += $unit->orders[$week][$target]['total'];
        }, 0);
    }

    /**
     * 年月単位の合計を算出
     *
     * @param  string $month
     * @param  string $display_unit
     * @return int|float
     */
    public function toSumPerMonth(string $month, string $display_unit)
    {
        $target = str_plural($display_unit);
        return $this->pluck("orders.{$target}.{$month}")->sum();
    }

    /**
     * 年月単位の総合計を算出
     *
     * @param  string $month
     * @param  string $display_unit
     * @return int|float
     */
    public function toSumOfWholePerMonth(string $month, string $display_unit)
    {
        return $this->reduce(function ($sum, $unit) use ($month, $display_unit) {
            if (property_exists($unit, 'factory_products')) {
                return $sum += $unit->factory_products->toSumPerMonth($month, $display_unit);
            }
            if (property_exists($unit, 'delivery_destinations')) {
                return $sum += $unit->delivery_destinations->toSumPerMonth($month, $display_unit);
            }

            $target = str_plural($display_unit);
            return $sum += $unit->orders[$target][$month];
        }, 0);
    }

    /**
     * 受注フォーキャストの情報が含まれていないかどうか判定する
     *
     * @param  mixed $week
     * @param  string $date
     * @return bool
     */
    public function notIncludeForecastedOrder($week, string $date): bool
    {
        return $this->reduce(function ($sum, $fp) use ($week, $date) {
            if (! property_exists($fp, 'delivery_destinations')) {
                return $sum;
            }

            return $sum += $fp->delivery_destinations->pluck("orders.{$week}.not_forecasted_order.{$date}")
                ->map(function ($not_forecasted_order) {
                    return $not_forecasted_order ? 0 : 1;
                })
                ->sum();
        }, 0) === 0;
    }

    /**
     * 確定注文のみかどうか判定する
     *
     * @param  mixed $week
     * @param  string $date
     * @return bool
     */
    public function isOnlyFixedOrder($week, string $date): bool
    {
        return $this->reduce(function ($sum, $fp) use ($week, $date) {
            if (! property_exists($fp, 'delivery_destinations')) {
                return $sum;
            }

            return $sum += $fp->delivery_destinations->pluck("orders.{$week}.only_fixed_order.{$date}")
                ->map(function ($only_fixed_order) {
                    return $only_fixed_order ? 0 : 1;
                })
                ->sum();
        }, 0) === 0;
    }

    /**
     * 工場ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByFactory(): BaseCollection
    {
        return $this->groupBy('factory_code')
            ->map(function ($grouped, $factory_code) {
                $latest_delivery_date = $grouped->first()->latest_delivery_date;
                if (! is_null($latest_delivery_date)) {
                    $latest_delivery_date = DeliveryDate::parse($latest_delivery_date);
                }

                return (object)[
                    'factory_code' => $factory_code,
                    'factory_abbreviation' => $grouped->first()->factory_abbreviation,
                    'latest_delivery_date' => $latest_delivery_date,
                    'orders' => $grouped
                ];
            })
            ->sortBy(function ($f, $key) {
                return $key;
            })
            ->values();
    }

    /**
     * 工場商品ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByFactoryProduct(): BaseCollection
    {
        return $this
            ->groupBy(function ($o) {
                return implode('|', [$o->factory_code, $o->factory_product_sequence_number]);
            })
            ->map(function ($grouped, $key) {
                [$facotry_code, $factory_product_sequence_number] = explode('|', $key);

                $latest_delivery_date = $grouped->first()->latest_delivery_date;
                if (! is_null($latest_delivery_date)) {
                    $latest_delivery_date = DeliveryDate::parse($latest_delivery_date);
                }

                return (object)[
                    'factory_code' => $facotry_code,
                    'factory_product_sequence_number' => (int)$factory_product_sequence_number,
                    'factory_product_name' => $grouped->first()->factory_product_name,
                    'factory_product_abbreviation' => $grouped->first()->factory_product_abbreviation,
                    'number_of_cases' => $grouped->first()->number_of_cases,
                    'unit' => $grouped->first()->unit,
                    'latest_delivery_date' => $latest_delivery_date,
                    'orders' => $grouped
                ];
            })
            ->values();
    }

    /**
     * 納入先ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByDeliveryDestination(): BaseCollection
    {
        return $this->groupBy('delivery_destination_code')
            ->map(function ($grouped, $delivery_destination_code) {
                $delivery_lead_time = ! is_null($grouped->first()->delivery_lead_time) ?
                    new DeliveryLeadTime($grouped->first()->delivery_lead_time) :
                    (new DeliveryLeadTime)->getDefaultDeliveryLeadTime();

                $shipment_lead_time = ! is_null($grouped->first()->shipment_lead_time) ?
                    new ShipmentLeadTime($grouped->first()->shipment_lead_time) :
                    (new ShipmentLeadTime)->getDefaultShipmentLeadTime();

                $latest_delivery_date = $grouped->first()->latest_delivery_date;
                if (! is_null($latest_delivery_date)) {
                    $latest_delivery_date = DeliveryDate::parse($latest_delivery_date);
                }

                return (object)[
                    'delivery_destination_code' => $delivery_destination_code,
                    'delivery_destination_abbreviation' => $grouped->first()->delivery_destination_abbreviation,
                    'delivery_lead_time' => $delivery_lead_time,
                    'shipment_lead_time' => $shipment_lead_time,
                    'latest_delivery_date' => $latest_delivery_date,
                    'orders' => $grouped
                ];
            })
            ->sortBy(function ($dd, $key) {
                return $key;
            })
            ->values();
    }

    /**
     * 製品規格ごとにグルーピング
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupByPackagingStyle(): BaseCollection
    {
        return $this
            ->groupBy(function ($o) {
                return implode('|', [$o->number_of_heads, $o->weight_per_number_of_heads, $o->input_group]);
            })
            ->map(function ($grouped, $key) {
                [$number_of_heads, $weight_per_number_of_heads, $input_group] = explode('|', $key);

                return (object)[
                    'number_of_heads' => $grouped->first()->number_of_heads,
                    'weight_per_number_of_heads' => $grouped->first()->weight_per_number_of_heads,
                    'input_group' => $grouped->first()->input_group,
                    'packaging_style_key' => $key,
                    'orders' => $grouped
                ];
            })
            ->values();
    }

    /**
     * 出荷日ごとにグルーピング
     *
     * @param  array $shipping_dates
     * @param  \App\Models\Master\Warehouse $warehouse
     * @return array
     */
    public function groupByShippingDate(array $shipping_dates, Warehouse $warehouse)
    {
        $list = [];
        foreach ($shipping_dates as $sd) {
            $list[] = $this
                ->filterByShippingDate($sd)
                ->groupBy('order_number')
                ->map(function ($grouped, $order_number) use ($warehouse) {
                    return (object)[
                        'order_number' => $order_number,
                        'order_quantity' => $grouped->first()->order_quantity,
                        'had_been_shipped' => $grouped->first()->hadBeenShipped(),
                        'warehouse_code' => $grouped->first()->warehouse_code ?: $warehouse->warehouse_code,
                        'product_allocations' => $grouped
                            ->reject(function ($pa) {
                                return is_null($pa->harvesting_date);
                            })
                            ->map(function ($pa) {
                                return (object)[
                                    'harvesting_date' => $pa->harvesting_date,
                                    'warehouse_code' => $pa->warehouse_code,
                                    'allocation_quantity' => (int)$pa->allocation_quantity
                                ];
                            })
                            ->all()
                    ];
                })
                ->values()
                ->reduce(function ($allocation, $o) use ($warehouse) {
                    $allocation->order_quantity += $o->order_quantity;
                    if ($o->had_been_shipped) {
                        $allocation->had_been_shipped = true;
                    }
                    if ($o->warehouse_code !== $warehouse->warehouse_code) {
                        $allocation->allocated_at_other_warehouse = true;
                    }

                    foreach ($o->product_allocations as $pa) {
                        $harvesting_date = $pa->harvesting_date;
                        if (! property_exists($allocation->allocation_quantities, $harvesting_date)) {
                            $allocation->allocation_quantities->{$harvesting_date} = $pa->allocation_quantity;
                            continue;
                        }

                        $allocation->allocation_quantities->{$harvesting_date} += $pa->allocation_quantity;
                    }

                    return $allocation;
                }, (object)[
                    'order_quantity' => 0,
                    'allocation_quantities' => (new stdClass),
                    'had_been_shipped' => false,
                    'allocated_at_other_warehouse' => false
                ]);
        }

        return $list;
    }

    /**
     * 出荷作業帳票単位でグルーピング
     *
     * @param  \App\Models\Master\Collections\EndUserCollection $end_users
     * @return array
     */
    public function groupToOutputShipmentFiles(?EndUserCollection $end_users = null): array
    {
        return $this
            ->groupBy(function ($o) {
                return implode('|', [
                    $o->shipping_date,
                    $o->delivery_date,
                    $o->end_user_code,
                    $o->delivery_destination_code,
                    $o->print_state
                ]);
            })
            ->map(function ($grouped) use ($end_users) {
                $group = [
                    'print_state' => $grouped->first()->print_state,
                    'shipping_date' => ShippingDate::parse($grouped->first()->shipping_date),
                    'delivery_date' => DeliveryDate::parse($grouped->first()->delivery_date),
                    'end_user_code' => $grouped->first()->end_user_code,
                    'delivery_destination_code' => $grouped->first()->delivery_destination_code,
                    'delivery_destination_name' => $grouped->first()->delivery_destination_name,
                    'delivery_destination_abbreviation' => $grouped->first()->delivery_destination_abbreviation,
                    'delivery_destination_postal_code' => $grouped->first()->delivery_destination_postal_code,
                    'delivery_destination_address' => $grouped->first()->delivery_destination_address,
                    'delivery_destination_phone_number' => $grouped->first()->delivery_destination_phone_number,
                    'disabled_to_display_price' => $grouped->first()->fsystem_statement_of_delivery_output_class ===
                        FsystemStatementOfDeliveryOutputClass::NOT_DISPLAY_PRICE,
                    'orders' => $grouped
                ];

                if ($end_users) {
                    $end_user = $end_users->findByEndUserCode($grouped->first()->end_user_code);
                    $group['end_user_abbreviation'] = $end_user->end_user_abbreviation ?? '';
                }

                return (object)$group;
            })
            ->values()
            ->all();
    }

    /**
     * 集荷時間ごとにグルーピング
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function groupByCollectionTime(): OrderCollection
    {
        return $this
            ->groupBy(function ($o) {
                return implode('|', [
                    $o->transport_company_code,
                    $o->collection_time_sequence_number
                ]);
            })
            ->values();
    }

    /**
     * 各帳票に金額を印字することが禁じられているか判定
     *
     * @return bool
     */
    public function isDisabledToPrintAmount(): bool
    {
        return $this
            ->filter(function ($o) {
                return $o->slip_status_type->value() === SlipStatusType::TEMP_ORDER;
            })
            ->isNotEmpty();
    }

    /**
     * 集荷依頼書出力単位でグルーピング
     *
     * @param  \App\Models\Master\Collections\EndUserCollection $end_users
     * @return array
     */
    public function groupToOutputCollectionRequests(EndUserCollection $end_users): array
    {
        return $this
            ->map(function ($o) use ($end_users) {
                $end_user = $end_users->findByEndUserCode($o->end_user_code);
                $o->end_user_abbreviation = $end_user->end_user_abbreviation ?? '';

                return $o;
            })
            ->groupBy(function ($o) {
                return implode('|', [
                    $o->shipping_date,
                    $o->transport_company_code,
                    $o->collection_time_sequence_number
                ]);
            })
            ->map(function ($grouped) {
                $group = [
                    'shipping_date' => $grouped->first()->shipping_date,
                    'transport_company_code' => $grouped->first()->transport_company_code,
                    'transport_company_name' => $grouped->first()->transport_company_name,
                    'transport_branch_name' => $grouped->first()->transport_branch_name,
                    'transport_company_abbreviation' => $grouped->first()->transport_company_abbreviation,
                    'tarnsport_company_phone_number' => $grouped->first()->tarnsport_company_phone_number,
                    'transport_company_fax_number' => $grouped->first()->transport_company_fax_number,
                    'collection_time' => $grouped->first()->collection_time,
                    'orders' => $grouped->map(function ($o) {
                        $o->printing_delivery_date = $o->getPrintingDeliveryDate();
                        $o->had_been_shipped = $o->hadBeenShipped();

                        return $o;
                    })
                ];

                return (object)$group;
            })
            ->values()
            ->all();
    }

    /**
     * 確定注文を抽出
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterFixed(): OrderCollection
    {
        return $this->filter(function ($o) {
            return $o->isFixedOrder();
        });
    }

    /**
     * 引当実績のある注文を抽出
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterAllocated(): OrderCollection
    {
        return $this->filter(function ($o) {
            return $o->isAllocated();
        });
    }

    /**
     * 引当済の注文を抽出
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterFullAllocated(): OrderCollection
    {
        return $this->filter(function ($o) {
            return $o->isFullAllocated();
        });
    }

    /**
     * 出荷済の注文を抽出
     *
     * @return \App\Models\Order\Collections\OrderCollection
     */
    public function filterShipped(): OrderCollection
    {
        return $this->filter(function ($o) {
            return $o->hadBeenShipped();
        });
    }

    /**
     * 引当済にするのに必要な製品数の合計を取得
     *
     * @return int
     */
    public function toSumOfProductQuantityToAllocateFull(): int
    {
        return $this
            ->map(function ($o) {
                return $o->getProductQuantityToAllocateFull();
            })
            ->sum();
    }

    /** 請求書(表紙)の出力単位でグルーピング
     *
     * @param  int $chunk_size
     * @return \Illuminate\Support\Collection
     */
    public function groupToInvoiceCover(int $chunk_size): BaseCollection
    {
        return $this
            ->groupBy(function ($o) {
                return implode('|', [$o->end_user_code, $o->delivery_destination_code]);
            })
            ->map(function ($grouped) {
                return (object)[
                    'end_user_name' => $grouped->first()->end_user_name,
                    'delivery_destination_name' => $grouped->first()->delivery_destination_name,
                    'sum_of_amount' => $grouped->sum('order_amount'),
                    'sum_of_tax' => $grouped->sum('tax_amount'),
                    'sum_of_amount_with_tax' => $grouped->sum('order_amount_with_tax'),
                    'sum_of_weight' => $grouped->sum('product_weight')
                ];
            })
            ->values()
            ->chunk($chunk_size);
    }

    /**
     * 請求書(明細)の出力単位でグルーピング
     *
     * @param  int $chunk_size
     * @return \Illuminate\Support\Collection
     */
    public function groupToInvoiceDetail(int $chunk_size): BaseCollection
    {
        return $this
            ->groupBy('end_user_code')
            ->map(function ($grouped) use ($chunk_size) {
                return (object)[
                    'end_user_name' => $grouped->first()->end_user_name,
                    'sum_of_amount' => $grouped->sum('order_amount'),
                    'sum_of_tax' => $grouped->sum('tax_amount'),
                    'sum_of_amount_with_tax' => $grouped->sum('order_amount_with_tax'),
                    'sum_of_weight' => $grouped->sum('product_weight'),
                    'chunked_orders' => $grouped->chunk($chunk_size)
                ];
            })
            ->values();
    }
}
