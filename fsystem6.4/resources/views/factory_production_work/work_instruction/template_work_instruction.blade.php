<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
</head>
<body>
  <table>
    <tbody>
      <tr>
        <td rowspan="3">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 28; font-weight: bold; border: 1px solid #000000">
          {{ $factory->factory_abbreviation }}&nbsp;{{ $species->species_name }}&nbsp;作業指示書/実績
        </td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="14" align="center" valign="middle" style="font-size: 16">
          {{ $working_date->formatWithDayOfWeek() }}
        </td>
      </tr>
    </tbody>
  </table>

  <table style="border: none">
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="font-size: 16">
          ・&nbsp;パネル(トレイ)枚数基準で記載。
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="font-size: 16">
          ・&nbsp;下段に作業実績と終了時刻を記入。
        </td>
      </tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 18">播種日</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 18">作業量</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 18">備考</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr></tr>
      <tr>
        <td rowspan="4">&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 18">{{ $seeding_stage['growing_stage_name'] }}</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 24; font-weight: bold">{{ $seeding_stage['seeding_tray_count'] }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr></tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="5" align="center" valign="middle" style="font-size: 10; font-weight: bold">トレイ</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 9">目標終了時間／実績</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="3" style="border-top: 1px solid #CCCCCC; background-color: #99ff99">&nbsp;</td>
        <td colspan="2" style="border-top: 1px solid #CCCCCC">&nbsp;</td>
      </tr>
      @foreach ($stages as $stage)
      @if (! ($loop->first || $loop->last))
        <tr>
          <td rowspan="4">&nbsp;</td>
          <td colspan="2" align="center" valign="middle" style="font-size: 18">{{ $stage['growing_stage_name'] }}</td>
          <td align="center" valign="middle" style="font-size: 14">{{ $stage['seeding_date'] }}</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td align="center" valign="middle" style="font-size: 24; font-weight: bold">{{ $stage['seeding_tray_count'] ?? $stage['panel_count_previous'] }}</td>
          <td>&nbsp;</td>
          <td align="center" valign="middle" style="font-size: 24; font-weight: bold">→</td>
          <td align="center" valign="middle" style="font-size: 36; font-weight: bold">{{ $stage['panel_count_next'] }}</td>
          <td>&nbsp;</td>
          <td align="center" valign="middle">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2" align="center" valign="middle" style="font-size: 12">目標タイム</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          @if (isset($stage['seeding_tray_count']))
            <td colspan="3" align="center" valign="middle" style="font-size: 10; font-weight: bold">トレイ</td>
          @else
            <td colspan="3" align="center" valign="middle" style="font-size: 10; font-weight: bold">パネル</td>
          @endif
          <td colspan="2" align="center" valign="middle" style="font-size: 10; font-weight: bold">パネル</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2" align="center" valign="middle" style="font-size: 9">目標終了時間／実績</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan="3" style="border-top: 1px solid #CCCCCC; background-color: #99ff99">&nbsp;</td>
          <td colspan="2" style="border-top: 1px solid #CCCCCC">&nbsp;</td>
        </tr>
        @endif
      @endforeach
      <tr>
        <td rowspan="4">&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 18">収穫</td>
        <td align="center" valign="middle" style="font-size: 14">{{ $harvesting_stage['seeding_date'] }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 24; font-weight: bold">{{ $harvesting_stage['harvesting_panel_count'] }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">目標タイム</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="5" align="center" valign="middle" style="font-size: 10; font-weight: bold">パネル</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 9">目標終了時間／実績</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="3" style="border-top: 1px solid #CCCCCC; background-color: #99ff99">&nbsp;</td>
        <td colspan="2" style="border-top: 1px solid #CCCCCC">&nbsp;</td>
      </tr>

      <tr>
        <td rowspan="4">&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 18">トリミング</td>
        <td align="center" valign="middle">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle" style="font-size: 24; font-weight: bold">{{ $harvesting_stage['harvesting_hole_count'] }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center" valign="middle">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 12">目標タイム</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="5" align="center" valign="middle" style="font-size: 10; font-weight: bold">株</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2" align="center" valign="middle" style="font-size: 9">目標終了時間／実績</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="3" style="border-top: 1px solid #CCCCCC; background-color: #99ff99">&nbsp;</td>
        <td colspan="2" style="border-top: 1px solid #CCCCCC">&nbsp;</td>
      </tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="font-weight: bold">特記事項</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="top" style="border: 1px solid #000000">&nbsp;</td>
      </tr>
      <tr></tr>
      <tr></tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="middle" style="font-weight: bold">作業報告</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left" valign="top" style="border: 1px solid #000000">&nbsp;</td>
      </tr>
    </tbody>
  </table>
</body>
</html>
