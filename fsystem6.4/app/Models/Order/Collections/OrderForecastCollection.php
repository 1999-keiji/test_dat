<?php

declare(strict_types=1);

namespace App\Models\Order\Collections;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use App\ValueObjects\Date\Date;
use App\ValueObjects\Date\DeliveryDate;
use App\ValueObjects\Date\ShippingDate;
use App\ValueObjects\Enum\ShipmentLeadTime;
use App\ValueObjects\Integer\DeliveryLeadTime;

class OrderForecastCollection extends Collection
{
    /**
     * 工場を条件に抽出
     *
     * @param  \stdClass $factory
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByFactory(stdClass $factory): OrderForecastCollection
    {
        return $this->where('factory_code', $factory->factory_code);
    }

    /**
     * 工場商品を条件に抽出
     *
     * @param  \stdClass $factory_product
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByFactoryProduct(stdClass $factory_product): OrderForecastCollection
    {
        return $this->filter(function ($of) use ($factory_product) {
            return $of->factory_code === $factory_product->factory_code &&
                $of->factory_product_sequence_number === $factory_product->factory_product_sequence_number;
        });
    }

    /**
     * 納入先を条件に抽出
     *
     * @param  \stdClass $delivery_destination
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByDeliveryDestination(stdClass $delivery_destination): OrderForecastCollection
    {
        return $this->where('delivery_destination_code', $delivery_destination->delivery_destination_code);
    }

    /**
     * 商品規格を条件に抽出
     *
     * @param  \stdClass $packaging_style
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByPackagingStyle(stdClass $packaging_style): OrderForecastCollection
    {
        return $this->filter(function ($of) use ($packaging_style) {
            return $of->number_of_heads === $packaging_style->number_of_heads &&
                $of->weight_per_number_of_heads === $packaging_style->weight_per_number_of_heads &&
                $of->input_group === $packaging_style->input_group;
        });
    }

    /**
     * 納入日もしくは納入日を条件に抽出
     *
     * @param  \App\ValueObjects\Date\Date $date
     * @return \App\Models\Order\Collections\OrderForecastCollection
     * @throws InvalidArgumentException
     */
    public function filterByDate(Date $date): OrderForecastCollection
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
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByDeliveryDate(DeliveryDate $delivery_date): OrderForecastCollection
    {
        return $this->where('date', $delivery_date->format('Y-m-d'));
    }

    /**
     * 出荷日を条件に抽出
     *
     * @param  \App\ValueObjects\Date\ShippingDate $shipping_date
     * @param  \App\ValueObjects\Enum\ShipmentLeadTime $shipment_lead_time
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByShippingDate(
        ShippingDate $shipping_date,
        ?ShipmentLeadTime $shipment_lead_time = null
    ): OrderForecastCollection {
        if (! is_null($shipment_lead_time) && $shipment_lead_time->willShipOnTheDate()) {
            $shipping_date = $shipping_date->subDay();
        }

        return $this->where('shipping_date', $shipping_date->format('Y-m-d'));
    }

    /**
     * 出荷年月を条件に抽出
     *
     * @param  string $shipping_month
     * @return \App\Models\Order\Collections\OrderForecastCollection
     */
    public function filterByShippingMonth(string $shipping_month): OrderForecastCollection
    {
        return $this->filter(function ($of) use ($shipping_month) {
            return $of->shipping_month === $shipping_month;
        });
    }

    /**
     * 注文数と商品重量の合計を取得
     *
     * @return array
     */
    public function toSumOfQuantityAndWeight(): array
    {
        return [
            'weight' => convert_to_kilogram($this->pluck('forecast_weight')->sum()),
            'quantity' => $this->pluck('forecast_number')->sum(),
            'only_fixed_order' => false
        ];
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
                    'order_forecasts' => $grouped
                ];
            })
            ->sortBy(function ($of, $key) {
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
            ->groupBy(function ($of) {
                return implode('|', [$of->factory_code, $of->factory_product_sequence_number]);
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
                    'latest_delivery_date' => $latest_delivery_date,
                    'order_forecasts' => $grouped
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
                    'order_forecasts' => $grouped
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
            ->groupBy(function ($of) {
                return implode('|', [$of->number_of_heads, $of->weight_per_number_of_heads, $of->input_group]);
            })
            ->map(function ($grouped, $key) {
                [$number_of_heads, $weight_per_number_of_heads, $input_group] = explode('|', $key);

                return (object)[
                    'number_of_heads' => $grouped->first()->number_of_heads,
                    'weight_per_number_of_heads' => $grouped->first()->weight_per_number_of_heads,
                    'input_group' => $grouped->first()->input_group,
                    'packaging_style_key' => $key,
                    'order_forecasts' => $grouped
                ];
            })
            ->values();
    }
}
