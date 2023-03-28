<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
</head>
<body>
  @php ($header_bg_color = '#c5d9f1;')
  @php ($border = '1px solid #000000')
  @php ($td = "border-top:${border}; border-left:${border}; border-right:${border}; border-bottom:${border};")
  <table>
    <thead>
      <tr>
        <th>
          {{ __('view.master.factories.factory') }}：{{ $factory->factory_abbreviation }}
        </th>
      </tr>
      <tr>
        <th>
          {{ __('view.shipment.global.delivery_date') }}：
          {{ $params['delivery_date']['from'] }}&nbsp;～&nbsp;{{ $params['delivery_date']['to'] }}
        </th></tr>
      <tr>
        <th>
          {{ __('view.master.customers.customer') }}：{{ $customer->customer_abbreviation ?? __('view.shipment.shipment_data_export.unselected') }}
        </th>
      </tr>
      <tr>
        <th>
          {{ __('view.master.end_users.end_user') }}：{{ $params['end_user_name'] ?: __('view.shipment.shipment_data_export.unselected') }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr></tr>
      <tr>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.shipment.global.shipping_date') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.shipment.global.harvesting_date') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.order.order_list.order_number') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.shipment.global.delivery_date') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.customers.customer_code') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.customers.customer_name') }}
        </th>
        <th align="center" width="24" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.end_users.end_user_code') }}
        </th>
        <th align="center" width="22" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.end_users.end_user_name') }}
        </th>
        <th align="center" width="16" style="{{ $td }}background-color:{{ $header_bg_color }}">
          {{ __('view.master.delivery_destinations.delivery_destination_code') }}
        </th>
        <th align="center" width="22" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.delivery_destinations.index_delivery_destination_name') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.species.species_code') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.species.species_name') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.products.product_code') }}
        </th>
        <th align="center" width="18" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.products.product_name') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.shipment.shipment_data_export.shipping_quantity') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.shipment.shipment_data_export.shipping_weight') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.order.order_list.order_unit') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.order.order_list.order_amount') }}
        </th>
        <th align="center" width="16" style="{{ $td }} background-color:{{ $header_bg_color }}">
          {{ __('view.master.global.currency_code') }}
        </th>
      </tr>
      @foreach ($product_allocations as $pa)
      <tr>
        <td align="center" style="{{ $td }}">{{ $pa->shipping_date->format('Y/m/d') }}</td>
        <td align="center" style="{{ $td }}">{{ $pa->harvesting_date->format('Y/m/d') }}</td>
        <td align="center" style="{{ $td }}">{{ $pa->order_number }}</td>
        <td align="center" style="{{ $td }}">{{ $pa->delivery_date->format('Y/m/d') }}</td>
        <td style="{{ $td }}">{{ $pa->customer_code }}</td>
        <td style="{{ $td }}">{{ $pa->customer_abbreviation }}</td>
        <td style="{{ $td }}">{{ $pa->end_user_code }}</td>
        <td style="{{ $td }}">{{ $pa->end_user_abbreviation }}</td>
        <td style="{{ $td }}">{{ $pa->delivery_destination_code }}</td>
        <td style="{{ $td }}">{{ $pa->delivery_destination_abbreviation }}</td>
        <td style="{{ $td }}">{{ $pa->species_code }}</td>
        <td style="{{ $td }}">{{ $pa->species_name }}</td>
        <td style="{{ $td }}">{{ $pa->product_code }}</td>
        <td style="{{ $td }}">{{ $pa->product_name }}</td>
        <td align="right" style="{{ $td }}" data-format='#,##0'>{{ $pa->allocation_quantity }}</td>
        <td align="right" style="{{ $td }}" data-format='#,##0'>{{ $pa->shipping_weight }}</td>
        <td align="right" style="{{ $td }}" data-format='#,##0'>{{ $pa->order_unit }}</td>
        <td align="right" style="{{ $td }}" data-format='#,##0'>{{ $pa->order_amount }}</td>
        <td align="center" style="{{ $td }}">{{ $pa->currency_code }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
