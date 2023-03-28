<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $header_style = 'background-color: #C5D9F1; border: 1px solid #000000;';
    $style = 'border: 1px solid #000000;';
    $expired_style = 'background-color: #FFBBFF; border: 1px solid #000000;';
  @endphp
</head>
<body>
  <table>
    <tr>
      <th height="30" style="fotn-weight: bold;">在庫一覧【{{ $factory->factory_abbreviation }}&nbsp;{{ $working_date->formatWithDayOfWeek() }}】</th>
    </tr>
    <tr>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.warehouses.warehouse_storage') }}</th>
      <th width="15" align="center" style="{{ $header_style }}">{{ __('view.global.status') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.species.species') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.factory_products.packaging_style') }}</th>
      <th width="25" align="center" style="{{ $header_style }}">
        {{ __('view.stock.stocks.stock_quantity') }}({{ __('view.global.pack_quantity') }})
      </th>
      <th width="15" align="center" style="{{ $header_style }}">{{ __('view.global.weight') }}(kg)</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.shipment.global.harvesting_date') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.stock.stocks.expired_on') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.order.order_list.delivery_date') }}</th>
      <th width="15" align="center" style="{{ $header_style }}">{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
      <th width="15" align="center" style="{{ $header_style }}">{{ __('view.stock.stocks.disposal_quantity') }}</th>
      <th width="15" align="center" style="{{ $header_style }}">{{ __('view.stock.stocks.disposal_weight') }}</th>
    </tr>
    @foreach ($stocks as $s)
    <tr>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ $s->storage_warehouse }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ array_flip($stock_status_list)[$s->stock_status] }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ $s->species_name }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">
        {{ $s->number_of_heads }}株
        {{ $s->weight_per_number_of_heads }}g
        {{ $input_group_list[$s->input_group] }}
      </td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}" data-format='#,##0'>{{ $s->stock_quantity }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}" data-format='#,##0.0'>{{ convert_to_kilogram($s->stock_weight) }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ $s->getHarvestingDate()->formatWithDayOfWeek() }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ $s->getExpiredOn()->formatWithDayOfWeek() }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">
      @if ($s->delivery_destination_abbreviation)
        {{ $s->delivery_destination_abbreviation }}
      @else
        未引当
      @endif
      </td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ $s->delivery_date }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}">{{ $s->delivery_lead_time }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}" data-format='#,##0'>{{ $s->disposal_quantity }}</td>
      <td style="{{ $s->isExpired() ? $expired_style : $style }}" data-format='#,##0.0'>{{ convert_to_kilogram($s->disposal_weight) }}</td>
    </tr>
    @endforeach
  </table>
</body>
</html>
