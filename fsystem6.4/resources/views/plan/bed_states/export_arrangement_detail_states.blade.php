<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
</head>
<body>
  @php ($border = '1px solid #000000')
  @php ($border_bold = '1px thick #000000')
  @php ($border_dashed = '1px dashed #000000')
  <table>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td height="60" colspan="70" align="center" valign="middle" style="font-size: 36; font-weight: bold">
          <span style="border-bottom: 1px double #000000;">
            {{ __('view.plan.planned_arrangement_status_work.detail') }} -
            {{ $bed_state->factory_species->factory->factory_abbreviation }} -
            {{ $bed_state->factory_species->species->species_name }} -
            {{ $working_date->formatToJa() }}
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
        <td height="50" colspan="4" style="background-color: #{{ $stage['label_color'] }}; border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }}; border-right: {{ $border }}">
          &nbsp;
        </td>
        <td colspan="8" align="center" valign="middle" style="font-size: 26; font-weight: bold; border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border }}; border-right: {{ $border_bold }}">
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
            colspan="{{ ($circulation['count'] * $factory_layout['rows']['avg']) + ($circulation['count'] - 1) }}"
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
            colspan="{{ $factory_layout['rows']['avg'] }}"
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
          <td colspan="{{ $factory_layout['rows']['avg'] }}" height="10" style="border-bottom: {{ $border_bold }}">&nbsp;</td>
          @php ($circulation = $column['circulation'])
        @endforeach
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>

      @php ($current = '')
      @foreach ($factory_layout['rows']['rows'] as $idx => $panels_per_row)
      <tr>
        <td
          width="8"
          align="center"
          valign="middle"
          style="font-size: 28; font-weight: bold; border-right: {{ $border_dashed }};">
          @if ($idx === $factory_layout['rows']['middle'])
          {{ $factory_layout['floor'] }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}
          @endif
        </td>
        <td
          align="center"
          middle="middle"
          style="font-size: 14; font-weight: bold; border-right: {{ $border_bold }};
          @if ($current !== '' && $current !== $panels_per_row['row'])
          border-top: {{ $border_bold }};
          @endif">
          @if ($panels_per_row['is_middle'])
          {{ $panels_per_row['row'] }}{{ __('view.master.factory_beds.row') }}
          @endif
        </td>
        @php ($circulation = '')
        @php ($column = '')
        @foreach ($panels_per_row['panels'] as $panel)
          @if (! $loop->first && $column !== $panel['column'])
          <td width="2" style="border-left: {{ $border_bold }}; border-right: {{ $border_bold }}">&nbsp;</td>
          @endif
          @if ($circulation !== '' && $circulation !== $panel['circulation'])
          <td width="2" style="border-left: {{ $border_bold }}; border-right: {{ $border_bold }}">&nbsp;</td>
          @endif
          <td
            width="8"
            style="
            @if ($current !== '' && $current !== $panels_per_row['row'])
            border-top: {{ $border_bold }};
            @endif
            @if ($loop->last)
            border-right: {{ $border_bold }};
            @endif
            @if (! $panel['other_species'] && $panel['label_color'])
            background-color: #{{ $panel['label_color'] }};
            @elseif ($panel['other_species'])
            background-color: #808080;
            @else
            background-color: #ffffff;
            @endif">
            &nbsp;
          </td>
          @php ($circulation = $panel['circulation'])
          @php ($column = $panel['column'])
        @endforeach
        <td
          align="center"
          middle="middle"
          style="font-size: 14; font-weight: bold;
          @if ($current !== '' && $current !== $panels_per_row['row'])
          border-top: {{ $border_bold }};
          @endif">
          @if ($panels_per_row['is_middle'])
          {{ $panels_per_row['row'] }}{{ __('view.master.factory_beds.row') }}
          @endif
        </td>
        <td
          width="8"
          align="center"
          valign="middle"
          style="font-size: 28; font-weight: bold; border-left: {{ $border_dashed }};">
          @if ($idx === $factory_layout['rows']['middle'])
          {{ $factory_layout['floor'] }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}
          @endif
        </td>
        @php ($current = $panels_per_row['row'])
      </tr>
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
          <td colspan="{{ $factory_layout['rows']['avg'] }}" height="10" style="border-top: {{ $border_bold }}">&nbsp;</td>
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
            colspan="{{ $factory_layout['rows']['avg'] }}"
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
            colspan="{{ ($circulation['count'] * $factory_layout['rows']['avg']) + ($circulation['count'] - 1) }}"
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
