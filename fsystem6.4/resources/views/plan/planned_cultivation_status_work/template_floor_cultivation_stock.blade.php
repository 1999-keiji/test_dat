<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  @php
    $border = '1px solid #000000';
    $border_bold = '2px thick #000000';
    $border_dashed = '1px dashed #000000';
    $border_double = '1px double #000000';
  @endphp
</head>
<body>
  <table>
    <tr>
      <th colspan="{{ (count($simulatable_dates) + 3) * count($factory_growing_stages) }}" align="center">
        {{ $config['floor_cultivation_stock']['sheet_title'] }}
        {{ $growth_simulation->factory_species->factory->factory_abbreviation }}&nbsp;-
        {{ $growth_simulation->factory_species->factory_species_name }}&nbsp;-
        {{ head($simulatable_dates)->formatWithDayOfWeek() }}&nbsp;～
        {{ last($simulatable_dates)->formatWithDayOfWeek() }}
        「{{ $growth_simulation->simulation_name }}」
      </th>
    </tr>
  </table>

  <table>
    <tr>
      @foreach ($factory_growing_stages as $fgs)
        <td colspan="{{ count($simulatable_dates) + 3 }}"
          align="center"
          style="border-top: {{ $border_bold }}; border-right: {{ $border_bold }}; border-bottom: {{ $border }}; border-left: {{ $border_bold }};">
          {{ $fgs->growing_stage_name }}
        </td>
      @endforeach
    </tr>
    <tr>
      @foreach ($factory_growing_stages as $fgs)
      <td width="7" style="border-bottom: {{ $border_double }}; border-left: {{ $border_bold }}"></td>
      <td width="7" style="border-bottom: {{ $border_double }}; border-left: {{ $border }}"></td>
      <td width="7" style="border-bottom: {{ $border_double }}; border-left: {{ $border }}"></td>
        @foreach ($simulatable_dates as $sd)
        <td
          width="7"
          align="center"
          style="border-bottom: {{ $border_double }};
            @if ($loop->last)
            border-right: {{ $border_bold }};
            @endif
            @if ($loop->first)
            border-left: {{ $border_double }};
            @else
            border-left: {{ $border_dashed }};
            @endif">
          {{ $sd->dayOfWeekJa() }}
        </td>
        @endforeach
      @endforeach
    </tr>
    @php ($current_row = $config['floor_cultivation_stock']['base_row'])
    @foreach ($base_patterns as $pattern => $grouped_patterns)
    <tr>
      @php ($current_column = $config['floor_cultivation_stock']['base_column'])
      @php ($number_of_patterns = $base_patterns->count())
      @foreach ($factory_growing_stages as $fgs)
        <td align="center"
          valign="middle"
          style="border-right: {{ $border }}; border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};"
          data-format='0"{{ __('view.master.factory_panels.hole') }}"'>
          {{ $fgs->number_of_holes }}
        </td>
        <td align="center" style="border-left: {{ $border }};
          @if (! $loop->parent->last)
          border-bottom: {{ $border_dashed }};
          @else
          border-bottom: {{ $border }};
          @endif">
          {{ $pattern }}
        </td>
        <td align="center" style="border-left: {{ $border }};
          @if (! $loop->parent->last)
          border-bottom: {{ $border_dashed }};
          @else
          border-bottom: {{ $border }};
          @endif">
          =SUM({{ implode(',', array_map(function ($floor) use ($current_row, $current_column, $number_of_patterns) {
            return get_excel_column_str($current_column).($current_row + ($number_of_patterns + 1) * $floor + 1);
          }, range(1, $growth_simulation->factory_species->factory->number_of_floors))) }})
        </td>
        @php ($current_column = $current_column + count($simulatable_dates) + 3)
        @foreach ($grouped_patterns as $fspi)
          @php ($pcsw = $planned_cultivation_status_works->findByGrowingStageSequenceNumberAndDayOfTheWeek($fgs->sequence_number, $fspi->day_of_the_week))
          <td align="center" style="
            @if ($loop->last && $loop->parent->last)
            border-right: {{ $border_bold }};
            @endif
            @if (! $loop->parent->parent->last)
            border-bottom: {{ $border_dashed }};
            @else
            border-bottom: {{ $border }};
            @endif
            @if ($loop->first)
            border-left: 1px double #000000;
            @else
            border-left: {{ $border_dashed }};
            @endif">
            {{ $pcsw ? ($pcsw->getMovingPanelCountPattern($loop->parent->parent->iteration) ?: 0) : 0 }}
          </td>
        @endforeach
      @endforeach
      @php ($current_row = $current_row + 1)
    </tr>
    @endforeach
    <tr>
      @php ($current_column = $config['floor_cultivation_stock']['base_column'])
      @foreach ($factory_growing_stages as $fgs)
        <td></td>
        <td align="center" style="border-left: {{ $border }};">{{ __('view.global.sum') }}</td>
        <td align="center" style="border-left: {{ $border }};">
          =SUM({{ implode(',', array_map(function ($row) use ($current_column, $base_patterns) {
            return get_excel_column_str($current_column).$row;
          }, range(($current_row - $base_patterns->count()), ($current_row - 1)))) }})
        </td>
        @foreach ($simulatable_dates as $idx => $sd)
        <td align="center" style="
          @if ($loop->last && $loop->parent->last)
          border-right: {{ $border_bold }};
          @endif
          @if ($loop->first)
          border-left: {{ $border_double }};
          @else
          border-left: {{ $border_dashed }};
          @endif">
          =SUM({{ implode(',', array_map(function ($row) use ($current_column, $base_patterns, $idx) {
            return get_excel_column_str($current_column + $idx + 1).$row;
          }, range(($current_row - $base_patterns->count()), ($current_row - 1)))) }})
        </td>
        @endforeach
        @php ($current_column = $current_column + count($simulatable_dates) + 3)
      @endforeach
    </tr>
    <tr>
      @php ($current_column = $config['floor_cultivation_stock']['base_column'])
      @php ($number_of_patterns = $base_patterns->count())
      @foreach ($factory_growing_stages as $fgs)
        <td colspan="2" align="center" style="border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};">
          {{ __('view.global.total') }}
        </td>
        <td align="center" style="border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border }};" data-format="0.0">
          =SUM({{ implode(',', array_map(function ($floor) use ($current_row, $current_column, $number_of_patterns) {
            return get_excel_column_str($current_column).($current_row + ($number_of_patterns + 1) * $floor + 1);
          }, range(1, $growth_simulation->factory_species->factory->number_of_floors))) }})
        </td>
        @foreach ($simulatable_dates as $idx => $sd)
        <td align="center" style="border-top: {{ $border_bold }}; border-bottom: {{ $border_bold }};
          @if ($loop->last && $loop->parent->last)
          border-right: {{ $border_bold }};
          @endif
          @if ($loop->first)
          border-left: {{ $border_double }};
          @else
          border-left: {{ $border_dashed }};
          @endif"
          data-format="#,##0;-#,##0">
          =SUM({{ implode(',', array_map(function ($floor) use ($current_row, $current_column, $number_of_patterns, $idx) {
            return get_excel_column_str($current_column + $idx + 1).($current_row + ($number_of_patterns + 1) * $floor + 1);
          }, range(1, $growth_simulation->factory_species->factory->number_of_floors))) }})
        </td>
        @endforeach
        @php ($current_column = $current_column + count($simulatable_dates) + 3)
      @endforeach
    </tr>
    @php ($header_row = $current_row)

    @php ($current_row = $current_row + 2)
    @foreach ($growth_simulation->factory_species->factory->factory_beds->groupByFloor()->reverse() as $floor => $grouped)
      @foreach ($base_patterns as $pattern => $grouped_patterns)
      <tr>
        @php ($current_column = $config['floor_cultivation_stock']['base_column'])
        @php ($hole_column = 1)
        @php ($number_of_patterns = $base_patterns->count())
        @foreach ($factory_growing_stages as $fgs)
          <td align="center"
            valign="middle"
            style="border-right: {{ $border }}; border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};">
            {{ $floor }}F
          </td>
          <td align="center" style="border-left: {{ $border }}; border-bottom: {{ $border_dashed }};">
            {{ $pattern }}
          </td>
          @php ($filtered = $planned_cultivation_status_works->filterByGrowingStageSequenceNumber($fgs->sequence_number))
          <td align="center"
            style="border-left: {{ $border }}; border-bottom: {{ $border_dashed }}; background-color: #{{ $fgs->label_color }}"
            data-format="0.0">
            {{ $filtered->isNotEmpty() ? ($filtered->first()->getMovingBedCountFloorPattern($floor, $loop->parent->iteration) ?:0 ) : 0 }}
          </td>
          @foreach ($grouped_patterns as $idx => $fspi)
            <td style="border-bottom: {{ $border_dashed }};
              @if ($loop->last && $loop->parent->last)
              border-right: {{ $border_bold }};
              @endif
              @if ($loop->first)
              border-left: {{ $border_double }};
              @else
              border-left: {{ $border_dashed }};
              @endif"
              data-format="#,##0;-#,##0">
              ={{ get_excel_column_str($current_column).$current_row }}*
                {{ get_excel_column_str($current_column + $idx + 1).(3 + $loop->parent->parent->iteration + 1) }}*
                {{ get_excel_column_str($hole_column) }}5
            </td>
          @endforeach
          @php ($current_column = $current_column + count($simulatable_dates) + 3)
          @php ($hole_column = $hole_column + 3 + count($simulatable_dates))
        @endforeach
        @php ($current_row = $current_row + 1)
      </tr>
      @endforeach
      <tr>
        @php ($current_column = $config['floor_cultivation_stock']['base_column'])
        @foreach ($factory_growing_stages as $fgs)
          <td align="center"
            valign="middle"
            style="border-right: {{ $border }}; border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};">
            {{ $floor }}F
          </td>
          <td align="center" style="border-bottom: {{ $border_bold }}; border-left: {{ $border }};">{{ __('view.global.sum') }}</td>
          <td align="center" style="border-bottom: {{ $border_bold }}; border-left: {{ $border }}; background-color: #{{ $fgs->label_color }}" data-format="0.0">
            =SUM({{ implode(',', array_map(function ($row) use ($current_column, $idx) {
              return get_excel_column_str($current_column).$row;
            }, range(($current_row - $base_patterns->count()), ($current_row - 1)))) }})
          </td>
          @foreach ($simulatable_dates as $idx => $sd)
          <td style="border-bottom: {{ $border_bold }};
            @if ($loop->last && $loop->parent->last)
            border-right: {{ $border_bold }};
            @endif
            @if ($loop->first)
            border-left: {{ $border_double }};
            @else
            border-left: {{ $border_dashed }};
            @endif"
            data-format="#,##0;-#,##0">
            =SUM({{ implode(',', array_map(function ($row) use ($current_column, $idx) {
              return get_excel_column_str($current_column + $idx + 1).$row;
            }, range(($current_row - $base_patterns->count()), ($current_row - 1)))) }})
          </td>
          @endforeach
          @php ($current_column = $current_column + count($simulatable_dates) + 3)
        @endforeach
        @php ($current_row = $current_row + 1)
      </tr>
    @endforeach

    <tr>
      @php ($current_column = $config['floor_cultivation_stock']['base_column'])
      @php ($number_of_patterns = $base_patterns->count())
      @foreach ($factory_growing_stages as $fgs)
        <td colspan="2" align="center" style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};">
          {{ __('view.global.total') }}
        </td>
        <td align="center" style="border-bottom: {{ $border_bold }}; border-left: {{ $border }};" data-format="0.0">
          =SUM({{ implode(',', array_map(function ($floor) use ($header_row, $current_column, $number_of_patterns) {
            return get_excel_column_str($current_column).($header_row + ($number_of_patterns + 1) * $floor + 1);
          }, range(1, $growth_simulation->factory_species->factory->number_of_floors))) }})
        </td>
        @foreach ($simulatable_dates as $idx => $sd)
        <td align="center" style="border-bottom: {{ $border_bold }};
          @if ($loop->last && $loop->parent->last)
          border-right: {{ $border_bold }};
          @endif
          @if ($loop->first)
          border-left: {{ $border_double }};
          @else
          border-left: {{ $border_dashed }};
          @endif"
          data-format="#,##0;-#,##0">
          =SUM({{ implode(',', array_map(function ($floor) use ($header_row, $current_column, $number_of_patterns, $idx) {
            return get_excel_column_str($current_column + $idx + 1).($header_row + ($number_of_patterns + 1) * $floor + 1);
          }, range(1, $growth_simulation->factory_species->factory->number_of_floors))) }})
        </td>
        @endforeach
        @php ($current_column = $current_column + count($simulatable_dates) + 3)
      @endforeach
    </tr>
  </table>
</body>
</html>
