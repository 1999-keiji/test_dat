<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
</head>
<body>
  <table>
    <tbody>
      <tr>
        <td rowspan="4">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 28; font-weight: bold; border: 1px solid #000000">
          {{ $factory->factory_abbreviation }}&nbsp;{{ $species->species_name }}&nbsp;製品化指示書/実績
        </td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr></tr>
      <tr>
        <td height="24">&nbsp;</td>
        <td colspan="15" align="center" valign="middle" style="font-size: 18">{{ $working_date->formatWithDayOfWeek() }}</td>
      </tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td width="2" height="54">&nbsp;</td>
        <td width="17" align="center" valign="middle">{{ $species->species_name }}</td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16">収穫予定<br>株数</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">調整予定株数<br>(収穫廃棄<br>/その他)</td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16">調整後株数<br>(予定)</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">調整実績株数<br>(収穫廃棄<br>/その他)</td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16">調整後株数<br>(実績)</td>
      </tr>
      <tr>
        <td height="27">&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold; background-color: #FCD5B4" data-format="#,##0">
          {{ $harvest_plan_shares }}
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; font-weight: bold; color: #FF0000" data-format="#,##0">
          {{ $forecasted_product_rate->crop_failure ?? '' }}
        </td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold;" data-format="#,##0">
          =C8+F8+F9
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; font-weight: bold; color: #FF0000" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $forecasted_product_rate->actual_crop_failure ?? '' }}
          @endif
        </td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold;" data-format="#,##0">
          =C8+K8+K9
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="27">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; font-weight: bold; color: #FF0000" data-format="#,##0">
          {{ $forecasted_product_rate->advanced_harvest ?? '' }}
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; font-weight: bold; color: #FF0000" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $forecasted_product_rate->actual_advanced_harvest ?? '' }}
          @endif
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="36">&nbsp;</td>
        <td valign="middle" style="color: #FF0000">※&nbsp;調整株数とは、収穫廃棄(-)、前採り(+)、未収穫(-)</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">製品化率<br>(予想)</td>
        <td colspan="3" align="center" valign="middle" style="font-size: 12">製品化重量<br>(予想)</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">製品化率<br>(実績)</td>
        <td colspan="3" align="center" valign="middle" style="font-size: 12">製品化重量<br>(実績)</td>
        <td width="9" align="center" valign="middle" style="font-size: 10">製品化重量<br>(１株)</td>
      </tr>
      <tr>
        <td height="36">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='0.00 %'>
          ={{ $forecasted_product_rate->product_rate ?? 0 }}/100
        </td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='#,##0.0" kg"'>
          =H8*F11*C{{ $iterative_data_end_column }}/1000
        </td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='0.00 %'>
          =IFERROR(O{{ $total_shares_culumn }}/M8, 0)
        </td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='#,##0.0" kg"'>
          =O{{ $total_weight_culumn }}
        </td>
        <td align="center" valign="middle" style="font-size: 16" data-format='#,##0"g"'>
          =K11*C{{ $iterative_data_end_column }}
        </td>
      </tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 12"></td>
        <td align="center" valign="middle" style="font-size: 12;">廃棄数</td>
        <td align="center" valign="middle" style="font-size: 12;" data-format="#,##0">=SUM(E14:P14)</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">トリミング</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">不具合品</td>
        <td colspan="3" align="center" valign="middle" style="font-size: 12">パッキング</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">検査サンプル</td>
        <td align="center" valign="middle" style="font-size: 12"></td>
      </tr>
      <tr>
        <td height="44">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16;" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $forecasted_product_rate->triming ?? '' }}
          @endif
        </td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16;" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $forecasted_product_rate->product_failure ?? '' }}
          @endif
        </td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16;" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $forecasted_product_rate->packing ?? '' }}
          @endif
        </td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16;" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $forecasted_product_rate->sample ?? '' }}
          @endif
        </td>
        <td colspan="3" align="center" valign="middle" style="font-size: 16;" data-format="#,##0"></td>
      </tr>
      <tr></tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16;">品種</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16;">作業順</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16;">商品規格</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16;">入数</td>
        <td colspan="4" align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #DCE6F1">作業指示</td>
        <td colspan="5" align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #FCD5B4; border-right: 1px medium #000000">作業実績</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #DCE6F1">数量</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #DCE6F1">株数</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #DCE6F1">補足</td>
        <td colspan="2" align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #FCD5B4">数量</td>
        <td align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #FCD5B4">株数</td>
        <td colspan="2" align="center" valign="middle" style="font-weight: bold; font-size: 16; background-color: #FCD5B4; border-right: 1px medium #000000">重量換算</td>
      </tr>
      @foreach ($details as $detail)
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 18; font-weight: bold">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16;">{{ $detail['packaging_style'] }}</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 18;" data-format="#,##0">
          {{ $detail['number_of_heads'] }}
        </td>
        <td align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1" data-format="#,##0">
          {{ $detail['crop_number'] }}
        </td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1" data-format="#,##0"></td>
        <td align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1" data-format="#,##0">
          @if ($working_date->willOutputProductizedResult())
          {{ $detail['product_quantity'] }}
          @endif
        </td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1" data-format="#,##0">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1; border-right: 1px medium #000000" data-format='#,##0.0" kg"'>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr></tr>
      <tr></tr>
      @endforeach
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 11; font-weight: bold">合計(株数)</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='#,##0"株"'>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="font-weight: bold">&nbsp;</td>
        <td style="font-weight: bold">&nbsp;</td>
        <td style="font-size: 16; font-weight: bold">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="font-size: 16; font-weight: bold">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold; border-right: 1px medium #000000" data-format='#,##0" 株"'>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr></tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 11; font-weight: bold; border-bottom: 1px medium #000000">合計(重量)</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='#,##0.0" kg"'>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="font-weight: bold">&nbsp;</td>
        <td style="font-weight: bold">&nbsp;</td>
        <td style="font-size: 16; font-weight: bold">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="font-size: 16; font-weight: bold">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold; border-right: 1px medium #000000" data-format='#,##0.0" kg"'>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr></tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="color: #FF0000">
          サンプル生産数は下記サンプル製品化指示を参照
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle">廃棄率</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format="0.00 %">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle">廃棄重量</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16; font-weight: bold" data-format='#,##0.0" kg"'>
          @if ($working_date->willOutputProductizedResult() && ($forecasted_product_rate ?? null))
          {{ $forecasted_product_rate->weight_of_discarded / 1000 }}
          @endif
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr></tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="font-size: 14; font-weight: bold">サンプル製品化指示</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16;">No.</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16;">商品規格</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16;">数量</td>
        <td colspan="10" align="center" valign="middle" style="font-size: 16;">備考</td>
      </tr>
      @foreach(range(1, 2) as $idx)
      <tr>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 16;">({{ $idx }})</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1">&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1">&nbsp;</td>
        <td colspan="10" align="center" valign="middle" style="font-size: 16; background-color: #DCE6F1">&nbsp;</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="font-size: 14; font-weight: bold">特記事項</td>
      </tr>
      @foreach (range(0, 6) as $i)
      <tr>
        <td>&nbsp;</td>
        @foreach (range(0, 14) as $j)
        <td
          align="left"
          valign="top"
          style="font-size: 18; background-color: #DCE6F1;
          @if ($loop->parent->first)
          border-top: 1px solid #000000;
          @endif
          @if ($loop->parent->last)
          border-bottom: 1px solid #000000;
          @endif
          @if ($loop->first)
          border-left: 1px solid #000000;
          @endif
          @if ($loop->last)
          border-right: 1px solid #000000;
          @endif">
          &nbsp;
        </td>
        @endforeach
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
