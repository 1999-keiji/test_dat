<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $header_style = 'background-color: #C5D9F1; border: 1px solid #000000;';
    $style = 'border: 1px solid #000000;';
  @endphp
</head>
<body>
  <table>
    <tr>
      <th>{{ __('view.global.export_condition') }}</th>
    </tr>
    <tr>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.factories.factory') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.warehouses.warehouse_storage') }}</th>
      <th width="10" align="center" style="{{ $header_style }}">{{ __('view.global.status') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.species.species') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.factory_products.packaging_style') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.shipment.global.harvesting_date') }}FROM</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.shipment.global.harvesting_date') }}TO</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.shipment.product_allocations.allocation_status') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.order.order_list.delivery_date') }}FROM</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.order.order_list.delivery_date') }}To</th>
      <th width="20" align="center" style="{{ $header_style }}">{{ __('view.stock.stocks.disposal_status') }}</th>
    </tr>
    <tr>
      <td style="{{ $style }}">{{ $factory->factory_abbreviation }}</td>
      <td style="{{ $style }}">
        @if ($params['warehouse_code'] ?? null)
        {{ $stocks->first()->storage_warehouse ?? '' }}
        @endif
      </td>
      <td style="{{ $style }}">{{ array_flip($stock_status_list)[$params['stock_status'] ?? null] ?? __('view.global.all') }}</td>
      <td style="{{ $style }}">
        @if ($params['species_code'] ?? null)
        {{ $stocks->first()->species_name ?? '' }}
        @endif
      </td>
      <td style="{{ $style }}">
        @if ($params['number_of_heads'])
        {{ $params['number_of_heads'] }}株
        {{ $params['weight_per_number_of_heads'] }}g
        {{ $input_group_list[$params['input_group']] }}
        @endif
      </td>
      <td style="{{ $style }}">{{ $params['harvesting_date_from'] ?? '' }}</td>
      <td style="{{ $style }}">{{ $params['harvesting_date_to'] ?? '' }}</td>
      <td style="{{ $style }}">{{ array_flip($allocation_status_list)[$params['allocation_status'] ?? null] ?? __('view.global.all') }}</td>
      <td style="{{ $style }}">{{ $params['delivery_destination_name'] ?? '' }}</td>
      <td style="{{ $style }}">{{ $params['delivery_date_from'] ?? '' }}</td>
      <td style="{{ $style }}">{{ $params['delivery_date_to'] ?? '' }}</td>
      <td style="{{ $style }}">{{ array_flip( $disposal_status_list_except_disposal)[$params['disposal_status']] }}</td>
    </tr>
  </table>
</body>
</html>
