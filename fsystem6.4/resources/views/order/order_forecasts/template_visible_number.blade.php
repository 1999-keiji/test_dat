<!DOCTYPE html>
<html lang={{ app()->getLocale() }}>
<head>
  <meta charset="UTF-8">
</head>
<body>
  <table>
    <thead>
      <tr>
        <td></td>
        <td style="font-weight: bold; font-size: 14;">{{ $factory->factory_abbreviation }}&nbsp;-&nbsp;{{ $species->species_name }}</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="center" valign="middle">（{{ __('view.global.quantity') }}）</td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td style="border-bottom: 2px thick #000;"></td>
        <td style="border-bottom: 2px thick #000;">{{ __('view.shipment.global.harvesting_date') }}</td>
        <td style="border-bottom: 2px thick #000;"></td>
        <td align="center" style="border-bottom: 2px thick #000;">{{ __('view.plan.growth_sale_management_summary.forwarded') }}</td>
        <td style="border-bottom: 2px thick #000;"></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $hd)
          @if (! $hd->isWorkingDay($factory))
          <td align="right" style="color: #FF0000; background-color: #F2DCDB; border-bottom: 2px thick #000;">{{ $hd->formatShortWithDayOfWeek() }}</td>
          @else
          <td align="right" style="border-bottom: 2px thick #000;">{{ $hd->formatShortWithDayOfWeek() }}</td>
          @endif
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>{{ __('view.plan.growth_sale_management.harvesting_quantity') }}</td>
        <td></td>
        <td></td>
        <td></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $hd)
          @if (! $hd->isWorkingDay($factory))
          <td style="background-color: #F2DCDB;"></td>
          @else
          <td data-format="#,##0">{{ $summary['harvesting_quantities'][$hd->format('W')][$hd->format('Ymd')] }}</td>
          @endif
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>{{ __('view.plan.growth_sale_management.product_rate') }}（%）</td>
        <td></td>
        <td></td>
        <td></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $hd)
          @if (! $hd->isWorkingDay($factory))
          <td style="background-color: #F2DCDB;"></td>
          @else
          <td data-format="0.00%">{{ (($summary['product_rates'][$hd->format('W')][$hd->format('Ymd')] ?: 0) / 100) }}</td>
          @endif
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td style="border-bottom: 1px double #000;"></td>
        <td style="border-bottom: 1px double #000;">{{ __('view.plan.growth_sale_management.product_weight') }}（kg）</td>
        <td style="border-bottom: 1px double #000;"></td>
        <td style="border-bottom: 1px double #000;"></td>
        <td style="border-bottom: 1px double #000;"></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $idx => $hd)
          @if (! $hd->isWorkingDay($factory))
          <td style="background-color: #F2DCDB; border-bottom: 1px double #000;"></td>
          @else
          <td style="border-bottom: 1px double #000;" data-format="#,##0.00">
            =(
              {{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}4*
              {{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}5
            )/10
          </td>
          @endif
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>{{ __('view.plan.growth_sale_management.order') }}（kg）</td>
        <td></td>
        <td></td>
        <td></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $idx => $hd)
        <td
          @if (! $hd->isWorkingDay($factory))
          style="background-color: #F2DCDB;"
          @endif
          data-format="#,##0.00">
          ={{ $config['visible_forecast']['sheet_name_weight'] }}!{{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}7
        </td>
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>{{ __('view.plan.growth_sale_management.gap') }}（kg）</td>
        <td></td>
        <td></td>
        <td></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $idx => $hd)
        <td
          @if (! $hd->isWorkingDay($factory))
          style="background-color: #F2DCDB;"
          @endif
          data-format="#,##0.00">
          ={{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}6
          -{{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}7
        </td>
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>{{ __('view.plan.growth_sale_management.discard') }}（kg）</td>
        <td></td>
        <td></td>
        <td></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $idx => $hd)
        <td
          @if (! $hd->isWorkingDay($factory))
          style="background-color: #F2DCDB;"
          @endif
          data-format="#,##0.00">
          {{ ($summary['disposal_weights'][$hd->format('W')][$hd->format('Ymd')] ?: 0) }}
        </td>
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td style="border-bottom: 1px double #000;"></td>
        <td style="border-bottom: 1px double #000;">{{ __('view.plan.growth_sale_management.stock') }}（kg）</td>
        <td style="border-bottom: 1px double #000;"></td>
        <td style="border-bottom: 1px double #000;" data-format="#,##0.00">
          {{ $summary['prev_carry_over_stock_weight'] }}
        </td>
        <td style="border-bottom: 1px double #000;"></td>
        @foreach ($harvesting_date->toListOfDate($delivery_date->getWeekTermOfExportOrderForecast()) as $idx => $hd)
        <td
          style="border-bottom: 1px double thick #000;
          @if (! $hd->isWorkingDay($factory))
          background-color: #F2DCDB;
          @endif"
          data-format="#,##0.00">
          =
          {{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column'] - ($loop->first ? 2 : 1)) }}10+
          {{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}8-
          {{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']) }}9
        </td>
        @endforeach
      </tr>
      <tr></tr>
      <tr>
        <td></td>
        <td></td>
        <td>{{ __('view.order.order_list.delivery_date') }}</td>
        <td></td>
        <td></td>
        <td></td>
        @foreach ($delivery_dates as $dd)
          @if (! $dd->isWorkingDay($factory))
          <td align="right" style="color: #FF0000;">{{ $dd->formatShortWithDayOfWeek() }}</td>
          @else
          <td align="right">{{ $dd->formatShortWithDayOfWeek() }}</td>
          @endif
        @endforeach
      </tr>
      <tr>
        <td></td>
        <td style="font-weight: bold;">{{ __('view.master.factory_products.factory_product') }}</td>
        <td style="font-weight: bold;">{{ __('view.master.delivery_destinations.delivery_destination') }}</td>
        <td style="font-weight: bold;">{{ __('view.master.delivery_warehouses.shipment_lead_time') }}</td>
        <td style="font-weight: bold;">{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</td>
        <td></td>
      </tr>
      <tr></tr>
      @foreach ($factory_products as $fp)
        @foreach ($fp->delivery_destinations as $dd)
        <tr>
          <td></td>
          <td valign="top"
            @if ($loop->parent->last)
            style="border-bottom: 1px thick #000;"
            @endif>{{ $fp->factory_product_abbreviation }}</td>
          <td>{{ $dd->delivery_destination_abbreviation }}</td>
          <td>{{ is_null($dd->shipment_lead_time) ? $shipment_lead_time->getDefaultShipmentLeadTime() : $dd->shipment_lead_time }}</td>
          <td>{{ is_null($dd->delivery_lead_time) ? $delivery_lead_time->getDefaultDeliveryLeadTime() : $dd->delivery_lead_time }}</td>
          <td>
            ={{ is_null($dd->shipment_lead_time) ? $shipment_lead_time->getDefaultShipmentLeadTime() : $dd->shipment_lead_time }}
            +{{ is_null($dd->delivery_lead_time) ? $delivery_lead_time->getDefaultDeliveryLeadTime() : $dd->delivery_lead_time }}
          </td>
          @foreach ($dd->delivery_dates as $delivery_date)
            <td @if (! $delivery_date['can_import']) style="background-color: #D3D3D3;" @endif>
              {{ $delivery_date['can_import'] ? $delivery_date['forecast'] : $delivery_date['order'] }}
            </td>
          @endforeach
        </tr>
        @endforeach
      @endforeach
    </tbody>
  </table>
</body>
</html>
