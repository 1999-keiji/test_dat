<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
</head>
<body>
  @php ($border = '1px solid #000000')
  @php ($border_bold = '2px thick #000000')
  @php ($border_dashed = '1px dashed #000000')
  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td height="60" colspan="50" align="center" valign="middle" style="font-size: 36; font-weight: bold">
          <span style="border-bottom: 1px double #000000;">
            {{ __('view.plan.planned_arrangement_status_work.index') }} -
            {{ $growth_simulation->factory_species->factory->factory_abbreviation }} -
            {{ $growth_simulation->factory_species->species->species_name }} -
            {{ $simulation_date->formatToJa() }} -
            「{{ $growth_simulation->simulation_name }}」
          <span>
        </td>
      </tr>
    </tbody>
  </table>
  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @foreach ($factory_growing_stages as $stage)
        <td height="50" colspan="2" style="background-color: #{{ $stage['label_color'] }}; border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }}; border-right: {{ $border }}">
          &nbsp;
        </td>
        <td colspan="4" align="center" valign="middle" style="font-size: 26; font-weight: bold; border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border }}; border-right: {{ $border_bold }}">
          {{ $stage['growing_stage_name'] }}({{ $stage['number_of_holes'] }})
        </td>
        @endforeach
      </tr>
    </tbody>
  </table>

  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @foreach ($factory_layout['circulations'] as $circulation)
          @if (! $loop->first)
          <td style="border-left: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td
            height="40"
            colspan="{{ $circulation['count'] + ($circulation['count'] - 1) }}"
            align="center"
            valign="middle"
            style="font-size:16; font-weight: bold;">
            {{ __('view.master.factory_columns.circulation') }} {{ $circulation['circulation'] }}
          </td>
          @if (! $loop->last)
          <td>&nbsp;</td>
          @endif
        @endforeach
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @php ($circulation = '')
        @foreach ($factory_layout['columns'] as $column)
          @if (! $loop->first)
          <td>&nbsp;</td>
          @endif
          @if ($circulation !== '' && $circulation !== $column['circulation'])
          <td style="border-left: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td
            height="50"
            align="center"
            valign="middle"
            style="font-size:11; font-weight: bold;">
            {{ __('view.master.factory_columns.line') }} - {{ $column['column_name'] }}
          </td>
          @php ($circulation = $column['circulation'])
        @endforeach
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @php ($circulation = '')
        @foreach ($factory_layout['columns'] as $column)
          @if (! $loop->first)
          <td>&nbsp;</td>
          @endif
          @if ($circulation !== '' && $circulation !== $column['circulation'])
          <td style="border-left: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td height="10" style="border-bottom: {{ $border_bold }}">&nbsp;</td>
          @php ($circulation = $column['circulation'])
        @endforeach
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>

      @php ($current = '')
      @foreach ($factory_layout['beds'] as $floor)
        @foreach ($floor['rows'] as $row)
        <tr>
          <td
            width="8"
            align="center"
            valign="middle"
            style="font-size: 28; font-weight: bold; border-right: {{ $border_dashed }};
            @if ($current !== '' && $current !== $floor['floor'])
            border-top: {{ $border_bold }}
            @endif">
            @if ($floor['middle'] === $row['row'])
            {{ $floor['floor'] }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}
            @endif
          </td>
          <td
            align="center"
            valign="middle"
            style="font-size: 14; font-weight: bold; border-bottom: {{ $border }};
            @if ($current !== '' && $current !== $floor['floor'])
            border-top: {{ $border_bold }}
            @endif">
            {{ $row['row'] }}{{ __('view.master.factory_beds.row') }}
          </td>
          @php ($circulation = '')
          @foreach ($row['beds'] as $bed)
            @if (! $loop->first)
            <td width="2">&nbsp;</td>
            @endif
            @if ($circulation !== '' && $circulation !== $bed['circulation'])
            <td width="2" style="border-left: {{ $border_bold }}">&nbsp;</td>
            @endif
            <td
              width="18"
              height="120"
              align="center"
              valign="middle"
              style="font-size: 28; font-weight: bold; border-bottom: {{ $border }}; border-left: {{ $border_bold }}; border-right: {{ $border_bold }};
              @if ($current !== '' && $current !== $floor['floor'])
              border-top: {{ $border_bold }};
              @else
              border-top: {{ $border }};
              @endif
              @if ($bed['stage'])
              background-color: #{{ $bed['label_color'] }};
              @endif
              @if ($bed['other_species'])
              background-color: #808080;
              @endif">
              {{ ($bed['stage'] && ! $bed['other_species']) ? ($bed[$label_of_bed] ?? '') : '' }}
            </td>
            @php ($circulation = $bed['circulation'])
          @endforeach
          <td
            align="center"
            valign="middle"
            style="font-size: 14; font-weight: bold; border-bottom: {{ $border }};
            @if ($current !== '' && $current !== $floor['floor'])
            border-top: {{ $border_bold }}
            @endif">
            {{ $row['row'] }}{{ __('view.master.factory_beds.row') }}
          </td>
          <td
            align="center"
            valign="middle"
            style="font-size: 28; font-weight: bold; border-left: {{ $border_dashed }};
            @if ($current !== '' && $current !== $floor['floor'])
            border-top: {{ $border_bold }}
            @endif">
            @if ($floor['middle'] === $row['row'])
            {{ $floor['floor'] }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}
            @endif
          </td>
        </tr>
        @php ($current = $floor['floor'])
        @endforeach
      @endforeach

      <tr>
        <td style="border-top: {{ $border_bold }}">&nbsp;</td>
        <td style="border-top: {{ $border_bold }}">&nbsp;</td>
        @php ($circulation = '')
        @foreach ($factory_layout['columns'] as $column)
          @if (! $loop->first)
          <td style="border-top: {{ $border_bold }}">&nbsp;</td>
          @endif
          @if ($circulation !== '' && $circulation !== $column['circulation'])
          <td style="border-top: {{ $border_bold }}; border-left: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td height="10" style="border-top: {{ $border_bold }}">&nbsp;</td>
          @php ($circulation = $column['circulation'])
        @endforeach
        <td style="border-top: {{ $border_bold }}">&nbsp;</td>
        <td style="border-top: {{ $border_bold }}">&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @php ($circulation = '')
        @foreach ($factory_layout['columns'] as $column)
          @if (! $loop->first)
          <td>&nbsp;</td>
          @endif
          @if ($circulation !== '' && $circulation !== $column['circulation'])
          <td style="border-left: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td
            height="50"
            align="center"
            valign="middle"
            style="font-size:11; font-weight: bold;">
            {{ __('view.master.factory_columns.line') }} - {{ $column['column_name'] }}
          </td>
          @php ($circulation = $column['circulation'])
        @endforeach
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @foreach ($factory_layout['circulations'] as $circulation)
          @if (! $loop->first)
          <td style="border-left: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td
            height="40"
            colspan="{{ $circulation['count'] + ($circulation['count'] - 1) }}"
            align="center"
            valign="middle"
            style="font-size:16; font-weight: bold;">
            {{ __('view.master.factory_columns.circulation') }} {{ $circulation['circulation'] }}
          </td>
          @if (! $loop->last)
          <td>&nbsp;</td>
          @endif
        @endforeach
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>
</body>
</html>
