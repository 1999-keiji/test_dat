<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
</head>
<body>
  @php ($fixed_order = '#ffff60')
  @php ($include_temporary_order = '#ffffff')
  <table>
    <thead>
      <tr>
        <td align="left" valign="middle" height="40" style="font-size: 18">
          {{ $factory->factory_abbreviation }}-{{ $species->species_name }}
        </td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td align="left" valign="middle" style="font-size: 18; font-weight: bold;">生産・販売管理表</td>
      </tr>
      <tr>
        <td width="6" height="14">収穫日</td>
        <td width="3"></td>
        <td width="7">収穫株数</td>
        <td width="7">調整後株数</td>
        <td width="7">予想製品化率</td>
        <td width="7">使用可能株数</td>
        <td width="7"></td>
        <td width="7">差引株数</td>
        <td width="6">基本発送日</td>
        <td width="3"></td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr></tr>
      <tr>
        <td height="90"></td>
      </tr>
      <tr></tr>

      @foreach ($date_list['date'] as $date)
        <tr>
          <td align="center" valign="middle">{{ $date['date']->format('m/d') }}</td>
          <td align="center" valign="middle">({{ $date['date']->dayOfWeekJa() }})</td>
          <td align="right" valign="middle">{{ $date['harvesting_quantity'] }}</td>
          <td align="right" valign="middle">
            ={{ $date['harvesting_quantity_cell'] }}+{{ $date['crop_failure_cell'] }}+{{ $date['advanced_harvest_cell'] }}
          </td>
          <td align="right" valign="middle">={{ $date['product_rate'] }}</td>
          <td align="right" valign="middle"></td>
          @if (! $date['can_import'])
            <td align="right" valign="middle" style="color: #FF0000;">{{ $date['plan_use_shares_reference'] }}</td>
          @else
            <td align="right" valign="middle">{{ $date['plan_use_shares_reference'] }}</td>
          @endif
          <td align="right" valign="middle"></td>
          <td align="center" valign="middle">{{ $date['shipping_date']->format('m/d') }}</td>
          <td align="center" valign="middle">({{ $date['shipping_date']->dayOfWeekJa() }})</td>
          @foreach ($date['details'] as $detail)
            <td align="right" valign="middle">{{ $detail['pack_number'] }}</td>
            <td align="right" valign="middle"></td>
            @foreach ($detail['orders'] as $orders_per_delivery_destination)
              @foreach ($orders_per_delivery_destination as $order)
                @php ($color = $order['will_ship_on_the_date'] && $order['quantity'] !== 0 ? '#ff0000' : '#000000')
                @if ($order['is_not_forecasted_order'])
                  @if ($order['only_fixed_order'])
                  <td align="right" valign="middle" style="font-weight:bold; color:{{ $color }}; background-color:{{ $fixed_order }};">
                    {{ $order['quantity'] }}
                  </td>
                  @endif
                  @if (! $order['only_fixed_order'])
                  <td align="right" valign="middle" style="font-weight:bold; color:{{ $color }}; background-color:{{ $include_temporary_order }};">
                    {{ $order['quantity'] }}
                  </td>
                  @endif
                @endif
                @if (! $order['is_not_forecasted_order'])
                <td align="right" valign="middle" style="color:{{ $color }};">{{ $order['quantity'] }}</td>
                @endif
              @endforeach
            @endforeach
            <td align="right" valign="middle"></td>
            <td align="right" valign="middle"></td>
            <td align="right" valign="middle"></td>
            <td align="right" valign="middle">{{ $detail['disposal_quantity']}}</td>
            <td align="right" valign="middle"></td>
          @endforeach
        </tr>
        <tr></tr>
        @if ($date['date']->isSunday())
        <tr></tr>
        @endif
      @endforeach
    </tbody>
  </table>
</body>
</html>
