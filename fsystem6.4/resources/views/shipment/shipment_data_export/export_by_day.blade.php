<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  @php ($header_bg_color = '#C5D9F1')
  @php ($holiday_bg_color = '#95B3D7')
  @php ($border = '1px solid #000000')
  @php ($border_bold = '2px medium #000000')
  @php ($td_date  = "border-top:${border};border-left:${border_bold};border-right:${border_bold};")
  @php ($td_first = "border-top:${border};border-left:${border_bold};border-right:${border};")
  @php ($td_mid   = "border-top:${border};border-left:${border};border-right:${border};")
  @php ($td_last  = "border-top:${border};border-left:${border};border-right:${border_bold};")
</head>
<body>
  <table>
    <thead>
      <tr>
        <th align="left">
          {{ __('view.master.factories.factory') }}：
          {{ $factory->factory_abbreviation }}
        </th>
      </tr>
      <tr>
        <th align="left">
          {{ __('view.shipment.global.harvesting_date') }}：
          {{ head($harvesting_dates)->format('Y/m/d') }}&nbsp;～
          {{ last($harvesting_dates)->format('Y/m/d') }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr></tr>
      <tr>
        <th
          width="14"
          style="border-top:{{ $border_bold }};border-left:{{ $border_bold }};border-right:{{ $border_bold }};background-color:{{ $header_bg_color }};">
        </th>
        @foreach ($productized_results->groupBySpecies() as $grouped)
        <th
          colspan="10"
          align="center"
          style="border-top:{{ $border_bold }};border-left:{{ $border_bold }};border-right:{{ $border_bold }};background-color:{{ $header_bg_color }};">
          {{ $grouped->first()->species_name }}
        </th>
        @endforeach
      </tr>
      <tr>
        <th
          align="center"
          style="{{ $td_date }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.global.harvesting_date') }}
        </td>
        @foreach ($productized_results->groupBySpecies() as $grouped)
        <th width="15" align="center" style="{{ $td_first }}background-color:{{$header_bg_color}};">
          {{ __('view.plan.growth_sale_management.product_rate') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.harvest_stock') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.crop_failure') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.advanced_harvest') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.other_failure') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.product_harvest_stock') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.used_weight') }}
        </th>
        <th width="20" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.unit_weight') }}
        </th>
        <th width="15" align="center" style="{{ $td_mid }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.failure_weight') }}
        </th>
        <th width="15" align="center" style="{{ $td_last }}background-color:{{$header_bg_color}};">
          {{ __('view.shipment.productized_results.failure_rate') }}
        </th>
        @endforeach
      </tr>
      @foreach ($harvesting_dates as $hd)
        @php ($style = '')
        @if ($loop->last)
          @php ($style .= "border-bottom:{$border_bold};")
        @endif
        @if (! $hd->isWorkingDay($factory))
          @php ($style .= "background-color:{$holiday_bg_color}")
        @endif
        <tr>
          <td align="left" style="{{ $td_date.$style }}">
            {{ $hd->format('m/d') }}（{{ $hd->dayOfWeekJa() }}）
          </td>
          @foreach ($productized_results->groupBySpecies() as $species_code => $grouped)
            @php ($pr = $grouped->findByHarvestingDate($hd))
            <td align="right" style="{{ $td_first.$style }}" data-format="0.00%">
              {{
                '=IFERROR(ROUNDUP('.
                get_excel_column_str(7 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '/('.
                get_excel_column_str(3 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '+'.
                get_excel_column_str(4 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '+'.
                get_excel_column_str(5 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '),4), "")'
              }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,###,##0" 株"'>
              {{ $pr->harvesting_quantity ?? '' }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,###,##0" 株"'>
              {{ $pr->crop_failure ?? '' }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,###,##0" 株"'>
              {{ $pr->advanced_harvest ?? '' }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,###,##0" 株"'>
              {{ optional($pr)->getSumOfDiscardedExceptFailure() ?: '' }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,###,##0" 株"'>
              {{ $pr->producted_quantity ?? '' }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,##0.0," kg"'>
              {{ $pr->producted_weight ?? '' }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,##0.0"g"'>
              {{
                '=IFERROR('.
                ($average_weights[$species_code] ?? 0).
                '*'.
                get_excel_column_str(2 + ($loop->index * 10)).($loop->parent->iteration + 5).
                ', "")'
              }}
            </td>
            <td align="right" style="{{ $td_mid.$style }}" data-format='#,##0.0," kg"'>
              {{ $pr->weight_of_discarded ?? '' }}
            </td>
            <td align="right" style="{{ $td_last.$style }}" data-format="0.00%">
              {{
                '=IFERROR(ROUNDUP('.
                get_excel_column_str(10 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '/('.
                get_excel_column_str(8 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '+'.
                get_excel_column_str(10 + ($loop->index * 10)).($loop->parent->iteration + 5).
                '),4), "")'
              }}
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
