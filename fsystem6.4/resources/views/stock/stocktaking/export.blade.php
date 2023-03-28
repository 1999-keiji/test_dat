<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $style = 'border: 1px solid #000000;';
  @endphp
</head>
<body>
  <table>
    <tr>
      <td width="2"></td>
      <td width="17"></td>
      <td width="17"></td>
      <td width="9"></td>
      <td width="9"></td>
      <td width="9"></td>
      <td width="12"></td>
      <td width="9"></td>
      <td width="12"></td>
      <td width="12"></td>
      <td width="12"></td>
      <td width="12"></td>
      <td width="2"></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="5" valign="middle" style="font-size: 24">{{ __('view.stock.stocktaking.stocktaking_file_name') }}</td>
      <td></td>
      <td></td>
      <td></td>
      <td align="center" valign="middle" style="{{ $style }}">工場長</td>
      <td align="center" valign="middle" style="{{ $style }}">担当者</td>
      <td align="center" valign="middle" style="{{ $style }}"></td>
      <td></td>
    </tr>
    <tr>
      @foreach (range(1, 13) as $column)
      <td></td>
      @endforeach
    </tr>
    <tr>
      <td></td>
      <td colspan="2" align="center">
        @if ($stocktaking->hasCompleted())
        {{ $stocktaking->getStocktakingCompletedAt()->format('Y年n月j日') }}
        @endif
      </td>
      @foreach (range(1, 6) as $column)
      <td></td>
      @endforeach
      <td style="{{ $style }}"></td>
      <td style="{{ $style }}"></td>
      <td style="{{ $style }}"></td>
    </tr>
    <tr>
      @foreach (range(1, 13) as $column)
      <td></td>
      @endforeach
    </tr>
    <tr>
      <td></td>
      <td colspan="4">
        {{ $stocktaking->factory->factory_name }}
      </td>
      @foreach (range(1, 11) as $column)
      <td></td>
      @endforeach
    </tr>
    @foreach (range(1, 2) as $row)
    <tr>
      @foreach (range(1, 13) as $column)
      <td></td>
      @endforeach
    </tr>
    @endforeach
  </table>

  <table>
    <tr height="21">
      @foreach (range(1, 10) as $column)
      <td></td>
      @endforeach
      <td valign="middle">(2)&nbsp;×&nbsp;{{ __('view.global.packaging') }}</td>
      <td valign="middle">(2)&nbsp;×&nbsp;{{ __('view.global.stock_quantity') }}</td>
      <td></td>
    </tr>
    <tr height="27">
      <td></td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000;">
        {{ __('view.master.species.species') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px hair #000000;">
        {{ __('view.master.delivery_destinations.delivery_destination') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px hair #000000;">
        {{ __('view.global.packaging') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px hair #000000;">
        {{ __('view.global.stock_quantity') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px hair #000000;">
        {{ __('view.global.quantity_per_case') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px hair #000000;">
        (1){{ __('view.stock.stocks.stock_quantity') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px hair #000000;">
        {{ __('view.global.unit') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px medium #000000; border-bottom: 1px solid #000000; border-left: 1px medium #000000;">
        (2){{ __('view.stock.stocktaking.actual_stock_quantity') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px medium #000000;">
        {{ __('view.stock.stocktaking.stock_difference') }}
      </td>
      <td align="center" valign="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000;">
        {{ __('view.global.weight') }}
      </td>
      <td align="center" valign="middle" style="{{ $style }}">
        {{ __('view.global.stock_quantity') }}
      </td>
    </tr>
    @foreach ($stocktaking_details as $idx => $sd)
    <tr height="27">
      <td></td>
      <td valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px solid #000000;">
        @if ($loop->first || (! $loop->first && $stocktaking_details[$idx - 1]->species_name !== $sd->species_name))
        {{ $sd->species_name }}
        @endif
      </td>
      <td valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px hair #000000;
        @if (! $sd->hasAllocated())
        color: #ff0000;
        @endif">
        {{ $sd->delivery_destination_abbreviation }}
      </td>
      <td align="right" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px hair #000000;" data-format='#,##0"g"'>
        {{ $sd->weight_per_number_of_heads }}
      </td>
      <td align="right" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px hair #000000;"data-format='#,###,##0.0" 株"'>
        {{ $sd->number_of_heads }}
      </td>
      <td align="right" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px hair #000000;">
        {{ $sd->number_of_cases ?: '' }}
      </td>
      <td align="right" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px hair #000000;" data-format="#,##0">
        {{ $sd->getStockQuantity() }}
      </td>
      <td align="center" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px hair #000000;">
        {{ $sd->unit }}
      </td>
      <td align="right" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px medium #000000;" data-format="#,##0">
        {{ $sd->getActualStockQuantity() }}
      </td>
      <td align="right" align="center" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px medium #000000;">
        {{ '=G'.($idx + 12).'-I'.($idx + 12) }}
      </td>
      <td align="right" valign="middle" style="border-bottom: 1px hair #000000; border-left: 1px solid #000000;" data-format="#,##0">
        @if ($sd->hasAllocated())
        {{ '=D'.($idx + 12).'*F'.($idx + 12).'*I'.($idx + 12) }}
        @else
        {{ '=D'.($idx + 12).'*G'.($idx + 12) }}
        @endif
      </td>
      <td align="right" valign="middle" style="border-right: 1px solid #000000; border-bottom: 1px hair #000000; border-left: 1px solid #000000;" data-format="#,##0">
        @if ($sd->hasAllocated())
        {{ '=E'.($idx + 12).'*F'.($idx + 12).'*I'.($idx + 12) }}
        @else
        {{ '=ROUNDDOWN(E'.($idx + 12).'*G'.($idx + 12).', 0)' }}
        @endif
      </td>
    </tr>
    @endforeach
    <tr height="27">
      <td></td>
      <td valign="middle" colspan="9" style="{{ $style }}">{{ __('view.global.sum') }}</td>
      <td align="right" valign="middle" style="{{ $style }}" data-format="#,##0">
        {{ '=SUM(K12:K'.(11 + $stocktaking_details->count()).')' }}
      </td>
      <td align="right" valign="middle" style="{{ $style }}" data-format="#,##0">
        {{ '=SUM(L12:L'.(11 + $stocktaking_details->count()).')' }}
      </td>
    </tr>
  </table>
</body>
