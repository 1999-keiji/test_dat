<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $header_style = 'background-color: #C5D9F1; border: 1px solid #000000; font-weight: bold;';
    $style = 'border: 1px solid #000000;';
  @endphp
</head>
<body>
  <table>
    <tr>
      <td valign="middle" height="30" style="font-size: 18; font-weight: bold;">
        {{ __('view.stock.stock_states.index') }}【{{ $factory->factory_abbreviation }}
        @if ($params['export_type'] === 'current')
        {{ $working_date->formatWithDayOfWeek() }}】
        @else
        {{ $working_date->parse($params['stock_date'])->formatWithDayOfWeek() }}】
        @endif
      </td>
    </tr>
    <tr height="30">
      <td width="20" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.master.warehouses.warehouse_storage') }}</td>
      <td width="10" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.global.status') }}</td>
      <td width="20" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.master.species.species') }}</td>
      <td width="25" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.master.factory_products.packaging_style') }}</td>
      <td width="12" align="center" valign="middle" style="{{ $header_style }}">
        {{ __('view.global.quantity') }}<br>({{ __('view.global.pack') }})
      </td>
      <td width="10" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.global.stock_quantity') }}</td>
      <td width="15" align="center" valign="middle" style="{{ $header_style }}">基本入株数</td>
      <td width="22" align="center" valign="middle" style="{{ $header_style }}">基本入数<br>あたり重量(kg)</th>
      <td width="12" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.global.weight') }}(kg)</th>
      <td width="15" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.shipment.global.harvesting_date') }}</th>
      <td width="20" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.stock.stocks.expired_on') }}</th>
      <td width="20" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
      <td width="15" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.global.quantity_per_case') }}</th>
      <td width="15" align="center" valign="middle" style="{{ $header_style }}">商品数<br>({{ __('view.global.case') }})</th>
      <td width="15" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.order.order_list.delivery_date') }}</th>
      <td width="15" align="center" valign="middle" style="{{ $header_style }}">{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
    </tr>
    @foreach ($stock_states->groupBySpecies() as $species)
      @foreach ($species->stocks as $ss)
      <tr>
        <td style="{{ $style }}">{{ $ss->warehouse_abbreviation }}</td>
        <td style="{{ $style }}">{{ array_flip($stock_status_list)[$ss->stock_status] }}</td>
        <td style="{{ $style }}">{{ $ss->species_abbreviation }}</td>
        <td style="{{ $style }}">
          {{ $ss->number_of_heads }}{{ __('view.global.stock') }}
          {{ $ss->weight_per_number_of_heads }}g
          {{ $input_group_list[$ss->input_group] }}
        </td>
        <td align="right" style="{{ $style }}" data-format="#,##0">{{ $ss->stock_quantity }}</td>
        <td align="right" style="{{ $style }}" data-format="#,##0">{{ $ss->stock_number }}</td>
        <td align="right" style="{{ $style }}">{{ $ss->number_of_heads }}</td>
        <td align="right" style="{{ $style }}">
          {{ convert_to_kilogram($ss->weight_per_number_of_heads) }}
        </th>
        <td align="right" style="{{ $style }}">
          {{ convert_to_kilogram($ss->stock_weight) }}
        </th>
        <td align="center" style="{{ $style }}">{{ $ss->getHarvestingDate()->formatWithDayOfWeek() }}</th>
        <td align="center" style="{{ $style }}">{{ $ss->getExpiredOn()->formatWithDayOfWeek() }}</th>
        <td style="{{ $style }}">
          {{ $ss->hasAllocated() ? $ss->delivery_destination_abbreviation : '未引当' }}
        </th>
        <td align="right" style="{{ $style }}">{{ $ss->number_of_cases }}</th>
        <td align="right" style="{{ $style }}">
          @if ($ss->hasAllocated())
          {{ $ss->stock_quantity / $ss->number_of_cases }}
          @endif
        </th>
        <td align="center" style="{{ $style }}">
          @if ($ss->hasAllocated())
          {{ $ss->getDeliveryDate()->formatWithDayOfWeek() }}
          @endif
        </th>
        <td align="center" style="{{ $style }}">
          @if ($ss->hasAllocated())
          {{ $ss->delivery_lead_time }}{{ __('view.global.day') }}
          @endif
        </th>
      </tr>
      @endforeach
      <tr>
        <td style="{{ $header_style }}">{{ __('view.global.total') }}</td>
        <td align="center" style="{{ $header_style }}">-</td>
        <td style="{{ $header_style }}">{{ $species->species_name }}</td>
        <td align="center" style="{{ $header_style }}">-</td>
        <td align="center" style="{{ $header_style }}">-</td>
        <td align="right" style="{{ $header_style }}" data-format="#,##0">{{ $species->stocks->sumOfStockNumber() }}</td>
        <td align="center" style="{{ $header_style }}">-</td>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="right" style="{{ $header_style }}">
          {{ convert_to_kilogram($species->stocks->sumOfStockWeight()) }}
        </th>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="center" style="{{ $header_style }}">-</th>
        <td align="center" style="{{ $header_style }}">-</th>
      </tr>
    @endforeach
  </table>
</body>
</html>
