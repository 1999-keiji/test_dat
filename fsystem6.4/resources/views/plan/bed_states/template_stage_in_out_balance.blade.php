<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  @php
    $border = '1px solid #000000';
    $border_bold = '2px thick #000000';
    $border_double = '1px double #000000';
  @endphp
</head>
<body>
<body>
  <table>
    <tr>
      <th colspan="{{ 2 + count($working_dates) * 3 }}" align="center">
        {{ $config['floor_cultivation_stock_sum']['sheet_title'] }}
        {{ $bed_state->factory_species->factory->factory_abbreviation }}&nbsp;-
        {{ $bed_state->factory_species->factory_species_name }}&nbsp;-
        {{ head($working_dates)->formatWithDayOfWeek() }}&nbsp;～
        {{ last($working_dates)->formatWithDayOfWeek() }}
      </th>
    </tr>
  </table>
  <table>
    <tbody>
      <tr>
        <th rowspan="2"
          align="center"
          valign="middle"
          style="border-top: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border_bold }};">
          {{ __('view.master.factory_beds.floor') }}
        </th>
        <th rowspan="2"
          align="center"
          valign="middle"
          style="border-top: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border }};">
          {{ __('view.master.factory_species.growing_stage') }}
        </th>
        @foreach ($working_dates as $wd)
        <th
          colspan="3"
          align="center"
          style="border-top: {{ $border_bold }}; border-bottom: {{ $border }}; border-left: {{ $border }};
          @if ($loop->last)
          border-right: {{ $border_bold }};
          @endif">
          {{ $wd->dayOfWeekJa() }}
        </th>
        @endforeach
      </tr>
      <tr>
        <td width="7"></td>
        <td width="25"></td>
        @foreach ($working_dates as $wd)
        <th width="15"
          align="center"
          style="border-bottom: {{ $border_double }}; border-left: {{ $border }};">
          {{ __('view.plan.planned_cultivation_status_work.growing_stock_quantity') }}
        </th>
        <th width="15" style="border-bottom: {{ $border_double }}; border-left: {{ $border }};"></th>
        <th width="15"
          align="center"
          style="border-bottom: {{ $border_double }}; border-left: {{ $border }};
          @if ($loop->last)
          border-right: {{ $border_bold }};
          @endif">
          {{ __('view.plan.planned_cultivation_status_work.excess_or_deficiency') }}
        </th>
        @endforeach
      </tr>
      @php
        $current_reference_column = $config['floor_cultivation_stock']['base_column'] + 1;
        $current_reference_row = $config['floor_cultivation_stock']['base_row'] + ($base_patterns->count() * 2) + 2;
        $current_row = $config['floor_cultivation_stock_sum']['base_row'];
      @endphp
      @foreach (range($bed_state->factory_species->factory->number_of_floors, 1) as $floor)
        @foreach ($factory_growing_stages as $fgs)
        <tr>
          @if ($loop->first)
          <th
            rowspan="{{ $factory_growing_stages->count() }}"
            align="center"
            valign="middle"
            style="border-bottom: {{ $border }}; border-left: {{ $border_bold }};">
            {{ $floor }}F
          </th>
          @else
          <th></th>
          @endif
          <th style="border-bottom: {{ $border }}; border-left: {{ $border }};">
            {{ $fgs->growing_stage_name }}:&nbsp;{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}
          </th>
          @php
            $current_column = $config['floor_cultivation_stock_sum']['base_column'];
          @endphp
          @foreach ($working_dates as $idx => $wd)
            <td style="border-bottom: {{ $border }}; border-left: {{ $border }};" data-format="#,##0">
              ={{ $config['floor_cultivation_stock']['sheet_title'] }}!{{ get_excel_column_str($current_reference_column + $idx).$current_reference_row }}
            </td>
            <td
              align="right"
              style="border-bottom: {{ $border }}; border-left: {{ $border }}; background-color: #FFC0CB;"
              data-format='#,##0"{{ __('view.master.factory_panels.panel') }}"'>
              =IFERROR(QUOTIENT({{ get_excel_column_str($current_column).$current_row }}, {{ $fgs->number_of_holes }}), 0)
            </td>
            @if ($loop->parent->last)
            <td style="border-bottom: {{ $border }}; border-left: {{ $border }};
              @if ($loop->last)
              border-right: {{ $border_bold }};
              @endif"></td>
            @else
            <td style="border-bottom: {{ $border }}; border-left: {{ $border }};
              @if ($loop->last)
              border-right: {{ $border_bold }};
              @endif"
              data-format="#,##0;[Red]-#,##0">
              =IFERROR({{ get_excel_column_str($current_column).($current_row + 1) }}, 0)-IFERROR({{ get_excel_column_str($current_column).$current_row }}, 0)
            </td>
            @endif
            @php
              $current_column = $current_column + 3;
            @endphp
          @endforeach
        </tr>
        @php
          $current_reference_column = $current_reference_column + count($working_dates) + 3;
          $current_row = $current_row + 1;
        @endphp
        @endforeach
        @php
          $current_reference_column = $config['floor_cultivation_stock']['base_column'] + 1;
          $current_reference_row = $current_reference_row + $base_patterns->count() + 1;
        @endphp
      @endforeach

      @php
        $current_row = $config['floor_cultivation_stock_sum']['base_row'];
      @endphp
      @foreach ($factory_growing_stages as $fgs)
        @php
          $current_column = $config['floor_cultivation_stock_sum']['base_column'];
        @endphp
        <tr>
          @if ($loop->first)
          <th
            rowspan="{{ $factory_growing_stages->count() }}"
            align="center"
            valign="middle"
            style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};">
            {{ __('view.global.total') }}
          </th>
          @else
          <th></th>
          @endif
          <th style="border-left: {{ $border }};
            @if ($loop->last)
            border-bottom: {{ $border_bold }};
            @else
            border-bottom: {{ $border }};
            @endif">
            {{ $fgs->growing_stage_name }}:&nbsp;{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}
          </th>
          @foreach ($working_dates as $idx => $wd)
            <td style="border-left: {{ $border }};
              @if ($loop->parent->last)
              border-bottom: {{ $border_bold }};
              @else
              border-bottom: {{ $border }};
              @endif"
              data-format="#,##0">
              =SUM({{ implode(',', array_map(function ($floor) use ($factory_growing_stages, $current_column, $current_row) {
                return get_excel_column_str($current_column).($current_row + ($floor * $factory_growing_stages->count()));
              }, range(0, ($bed_state->factory_species->factory->number_of_floors - 1)))) }})
            </td>
            <td
              align="right"
              style="border-left: {{ $border }};
                @if ($loop->parent->last)
                border-bottom: {{ $border_bold }};
                @else
                border-bottom: {{ $border }};
                @endif"
              data-format='#,##0"{{ __('view.master.factory_panels.panel') }}"'>
              =SUM({{ implode(',', array_map(function ($floor) use ($factory_growing_stages, $current_column, $current_row) {
                return 'IFERROR('.
                  get_excel_column_str($current_column + 1).($current_row + ($floor * $factory_growing_stages->count())).
                  ',0)';
              }, range(0, ($bed_state->factory_species->factory->number_of_floors - 1)))) }})
            </td>
            @if ($loop->parent->last)
              <td style="border-left: {{ $border }};
              @if ($loop->last)
              border-right: {{ $border_bold }};
              @endif
              @if ($loop->parent->last)
              border-bottom: {{ $border_bold }};
              @else
              border-bottom: {{ $border }};
              @endif"></td>
            @else
              <td style="border-left: {{ $border }};
              @if ($loop->last)
              border-right: {{ $border_bold }};
              @endif
              @if ($loop->parent->last)
              border-bottom: {{ $border_bold }};
              @else
              border-bottom: {{ $border }};
              @endif"
              data-format="#,##0;[Red]-#,##0">
                =SUM({{ implode(',', array_map(function ($floor) use ($factory_growing_stages, $current_column, $current_row) {
                  return get_excel_column_str($current_column + 2).($current_row + ($floor * $factory_growing_stages->count()));
                }, range(0, ($bed_state->factory_species->factory->number_of_floors - 1)))) }})
              </td>
            @endif
            @php
              $current_column = $current_column + 3;
            @endphp
          @endforeach
          @php
            $current_row = $current_row + 1;
          @endphp
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
