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
    <tr></tr>
    <tr>
      <td valign="middle" style="font-size: 14; font-weight: bold;">
        {{ __('view.stock.stocktaking.stocktaking') }}{{ __('view.stock.stocktaking.transition_file') }}:
        {{ $stocktaking->factory->factory_abbreviation }}&nbsp;
        {{ $stocktaking->warehouse->warehouse_abbreviation }}
      </td>
    </tr>
    <tr>
      <td valign="middle" style="font-size: 14; font-weight: bold;">
        {{ __('view.stock.stocktaking.stocktaking_month') }}:{{ $stocktaking->stocktaking_month }}
      </td>
    </tr>
  </table>

  @foreach ($species_list as $s)
    <tr>
      <td></td>
      <td valign="middle" style="font-size: 14; font-weight: bold;">
        {{ __('view.master.species.species') }}:{{ $s->species_name }}
      </td>
    </tr>
    <tr>
      <td></td>
      <td rowspan="2" width="24" align="center" valign="middle" style="{{ $header_style }}">
        {{ __('view.master.factory_products.packaging_style') }}
      </td>
      <td rowspan="2" align="center" valign="middle" style="{{ $header_style }}">
        {{ __('view.master.factory_products.number_of_heads') }}
      </td>
      <td colspan="2" align="center" style="{{ $header_style }}">
        {{ __('view.stock.stocktaking.stocktaking_quantity') }}
      </td>
      @foreach ($base_dates as $date)
      <td colspan="3" align="center" style="{{ $header_style }}">{{ $date->format('n/j') }}</td>
      @endforeach
      <td colspan="3" align="center" style="{{ $header_style }}">
        {{ __('view.global.end_of_month') }}{{ __('view.stock.global.stock') }}
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td width="11" align="center" style="{{ $header_style }}">
        {{ __('view.stock.stocktaking.actual_stock_quantity') }}
      </td>
      <td align="center" style="{{ $header_style }}">
        {{ __('view.global.weight') }}(kg)
      </td>
      @foreach ($base_dates as $date)
      <td align="center" style="{{ $header_style }}">
        {{ __('view.plan.global.product') }}
      </td>
      <td align="center" style="{{ $header_style }}">
        {{ __('view.shipment.product_allocations.allocate') }}
      </td>
      <td align="center" style="{{ $header_style }}">
        {{ __('view.shipment.shipment_fix.ship') }}
      </td>
      @endforeach
      <td width="11" align="center" style="{{ $header_style }}">
        {{ __('view.stock.stocktaking.actual_stock_quantity') }}
      </td>
      <td align="center" style="{{ $header_style }}">
        {{ __('view.global.weight') }}(kg)
      </td>
      <td align="center" style="{{ $header_style }}">
        {{ __('view.global.stock_quantity') }}
      </td>
    </tr>
    @foreach ($s->stock_styles as $ss)
    <tr>
      <td></td>
      <td style="{{ $style }}">
        {{ $ss->number_of_heads }}{{ __('view.global.stock') }}
        {{ $ss->weight_per_number_of_heads }}g
        {{ $input_group_list[$ss->input_group] }}
      </td>
      <td align="{{ $ss->hasAllocated() ? 'right' : 'center' }}" style="{{ $style }}">
        {{ $ss->number_of_cases ?: '-' }}
      </td>
      <td style="{{ $style }}">{{ $ss->actual_stock_quantity }}</td>
      <td style="{{ $style }}">{{ convert_to_kilogram($ss->stock_weight) }}</td>
      @foreach ($base_dates as $date)
      <td align="right" style="{{ $style }}" data-format='0;"▲ "0'>
        {{ $ss->dates[$date->format('Ymd')]->producted }}
      </td>
      <td align="right" style="{{ $style }}" data-format='0;"▲ "0'>
        {{ $ss->dates[$date->format('Ymd')]->allocated }}
      </td>
      <td align="right" style="{{ $style }}" data-format='0;"▲ "0'>
        {{ $ss->dates[$date->format('Ymd')]->shipped }}
      </td>
      @endforeach
      <td align="right" style="{{ $style }}" data-format='0;"▲ "0'>
        {{ $ss->current_stock_quantity }}
      </td>
      <td align="right" style="{{ $style }}" data-format="#,##0.0">
        ={{ $ss->current_stock_quantity }}*{{ $ss->weight_per_number_of_heads }}/1000
      </td>
      <td align="right" style="{{ $style }}" data-format="#,##0">
        ={{ $ss->current_stock_quantity }}*{{ $ss->number_of_heads }}
      </td>
    </tr>
    @endforeach
    <tr></tr>
  @endforeach
</body>
