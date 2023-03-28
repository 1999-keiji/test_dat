<!doctype html>
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
  <table>
    <tr>
      <th colspan="{{ 6 + count($working_dates) * 3 }}" align="center">
        {{ $config['cultivation_simulation']['sheet_title'] }}
        {{ $bed_state->factory_species->factory->factory_abbreviation }}&nbsp;-
        {{ $bed_state->factory_species->factory_species_name }}&nbsp;-
        {{ head($working_dates)->formatWithDayOfWeek() }}&nbsp;～
        {{ last($working_dates)->formatWithDayOfWeek() }}
      </th>
    </tr>
  </table>
  <table>
    <tr>
      <td width="1"></td>
      <td
        width="15"
        rowspan="2"
        align="center"
        valign="middle"
        style="border-top: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border_bold }};">
        {{ __('view.master.factory_species.growing_stage') }}
      </td>
      <td
        width="13"
        rowspan="2"
        align="center"
        valign="middle"
        style="border-top: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border }};">
        {{ __('view.master.factory_cycle_patterns.pattern') }}
      </td>
      @foreach ($working_dates as $wd)
      <td
        colspan="3"
        align="center"
        style="border-top: {{ $border_bold }}; border-bottom: {{ $border }};
        @if ($loop->first)
        border-left: {{ $border_double }};
        @else
        border-left: {{ $border_bold }};
        @endif">
        {{ $wd->dayOfWeekJa() }}
      </td>
      @endforeach
      <td
        width="11"
        rowspan="2"
        align="center"
        valign="middle"
        style="border-top: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border_bold }};">
        Ｐ入数/Ｂ
      </td>
      <td
        width="11"
        rowspan="2"
        align="center"
        valign="middle"
        style="border-top: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border_bold }};">
        パネル規格
      </td>
      <td
        width="17"
        rowspan="2"
        align="center"
        valign="middle"
        style="border-top: {{ $border_bold }}; border-right: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border }};">
        1週間の収穫株数
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      @foreach ($working_dates as $wd)
      <td
        width="11"
        align="center"
        style="border-bottom: {{ $border_double }};
        @if ($loop->first)
        border-left: {{ $border_double }};
        @else
        border-left: {{ $border_bold }};
        @endif">
        {{ __('view.plan.planned_cultivation_status_work.panel_quantity') }}
      </td>
      <td
        width="11"
        align="center"
        style="border-bottom: {{ $border_double }}; border-left: {{ $border }};">
        {{ __('view.plan.planned_cultivation_status_work.bed_quantity') }}
      </td>
      <td
        width="17"
        align="center"
        style="border-right: {{ $border_bold }}; border-bottom: {{ $border_double }}; border-left: {{ $border }};">
        収穫パネル数
      </td>
      @endforeach
    </tr>

    @php
      $current_reference_column = $config['floor_cultivation_stock']['base_column'];
      $current_reference_row = $config['floor_cultivation_stock']['base_row'];
      $current_row = $config['cultivation_simulation']['base_row'];
    @endphp
    @foreach ($factory_growing_stages as $fgs)
      @foreach ($fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern() as $pattern => $grouped_patterns)
        <tr>
          <td></td>
          @if ($loop->first)
          <td
            rowspan="{{ $loop->count + 2 }}"
            align="center"
            valign="middle"
            style="border-left: {{ $border_bold }};
            @if ($loop->parent->last)
            border-bottom: {{ $border_bold }};
            @else
            border-bottom: {{ $border_double }};
            @endif">
            {{ $fgs->growing_stage_name }}({{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }})
          </td>
          @else
          <td></td>
          @endif
          <th
            align="center"
            style="border-left: {{ $border }};
            @if ($loop->last)
            border-bottom: {{ $border_double }};
            @else
            border-bottom: {{ $border }};
            @endif">{{ $pattern }}
          </th>
          @php
            $current_column = $config['cultivation_simulation']['base_column'];
          @endphp
          @foreach ($working_dates as $idx => $wd)
            <td style="background-color: #00FFFF;
              @if ($loop->first)
              border-left: {{ $border_double }};
              @else
              border-left: {{ $border_bold }};
              @endif
              @if ($loop->parent->last)
              border-bottom: {{ $border_double }};
              @else
              border-bottom: {{ $border }};
              @endif">
              ={{ $config['floor_cultivation_stock']['sheet_title'] }}!{{ get_excel_column_str($current_reference_column + $idx + 1).($current_reference_row) }}
            </td>
            <td style="border-left: {{ $border }};
              @if ($loop->parent->last)
              border-bottom: {{ $border_double }};
              @else
              border-bottom: {{ $border }};
              @endif">
              ={{ $config['floor_cultivation_stock']['sheet_title'] }}!{{ get_excel_column_str($current_reference_column).($current_reference_row) }}
            </td>
            <td style="border-left: {{ $border }};
              @if ($loop->parent->last)
              border-bottom: {{ $border_double }};
              @else
              border-bottom: {{ $border }};
              @endif">
              ={{ get_excel_column_str($current_column).$current_row }}*{{ get_excel_column_str($current_column + 1).$current_row }}
            </td>
            @php
              $current_column = $current_column + 3;
            @endphp
          @endforeach
          <td style="background-color: #00FFFF; border-left: {{ $border_bold }};
            @if ($loop->last)
            border-bottom: {{ $border_double }};
            @else
            border-bottom: {{ $border }};
            @endif">
            ={{ implode('+', array_map(function ($idx) use ($current_column, $current_row) {
              return get_excel_column_str($current_column - $idx * 3).$current_row;
            }, range(count($working_dates), 1))) }}
          </td>
          <td style="border-left: {{ $border_bold }};
            @if ($loop->last)
            border-bottom: {{ $border_double }};
            @else
            border-bottom: {{ $border }};
            @endif"></td>
          <td style="border-right: {{ $border_bold }}; border-left: {{ $border }};
            @if ($loop->last)
            border-bottom: {{ $border_double }};
            @else
            border-bottom: {{ $border }};
            @endif"></td>
        </tr>
        @php
          $current_reference_row = $current_reference_row + 1;
          $current_row = $current_row + 1;
        @endphp
      @endforeach
      <tr>
        <td></td>
        <td></td>
        <th align="center" style="border-bottom: {{ $border }}; border-left: {{ $border }};">{{ __('view.global.sum') }}</th>
        @php
          $current_column = $config['cultivation_simulation']['base_column'];
        @endphp
        @foreach ($working_dates as $idx => $wd)
          <td style="border-bottom: {{ $border }};
            @if ($loop->first)
            border-left: {{ $border_double }};
            @else
            border-left: {{ $border_bold }};
            @endif"></td>
          <td style="background-color: #FFC0CB; border-bottom: {{ $border }}; border-left: {{ $border }};">
            =SUM({{ get_excel_column_str($current_column + 1).($current_row - $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count()) }}:{{ get_excel_column_str($current_column + 1).($current_row - 1) }})
          </td>
          <td style="background-color: #ffff00; border-bottom: {{ $border }}; border-left: {{ $border }};">
            =SUM({{ get_excel_column_str($current_column + 2).($current_row - $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count()) }}:{{ get_excel_column_str($current_column + 2).($current_row -1 ) }})
          </td>
          @php
            $current_column = $current_column + 3;
          @endphp
        @endforeach
        <td style="background-color: #00FFFF; border-bottom: {{ $border }}; border-left: {{ $border_bold }};">
          ={{ implode('+', array_map(function ($idx) use ($current_column, $current_row) {
            return get_excel_column_str($current_column - ($idx * 3) + 2).$current_row;
          }, range(count($working_dates), 1))) }}
        </td>
        <td style="background-color: #FFC0CB; border-bottom: {{ $border }}; border-left: {{ $border_bold }};">
          {{ $fgs->number_of_holes }}
        </td>
        <td style="background-color: #FFC0CB; border-right: {{ $border_bold }}; border-bottom: {{ $border }}; border-left: {{ $border }};" data-format="#,##0">
          ={{ get_excel_column_str($current_column).($current_row) }}*{{ get_excel_column_str($current_column + 1).($current_row) }}
        </td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <th align="center" style="border-left: {{ $border }};
          @if ($loop->last)
          border-bottom: {{ $border_bold }};
          @else
          border-bottom: {{ $border_double }};
          @endif">
          {{ __('view.global.stock') }}{{ __('view.global.sum') }}
        </th>
        @php
          $current_column = $config['cultivation_simulation']['base_column'];
        @endphp
        @foreach ($working_dates as $idx => $wd)
          <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border_double }};"></td>
          <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border }};"></td>
          <td style="background-color: #90ee90; border-bottom: {{ $border_bold }}; border-left: {{ $border }};" data-format="#,##0">
            ={{ get_excel_column_str($current_column + 2).($current_row) }}*{{ get_excel_column_str(($current_column - $idx * 3) + (count($working_dates) * 3) + 1).($current_row) }}
          </td>
          @php
            $current_column = $current_column + 3;
          @endphp
        @endforeach
        <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};"></td>
        <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};"></td>
        <td style="border-right: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border }};"></td>
      </tr>
      @php
        $current_reference_column = $current_reference_column + count($working_dates) + 3;
        $current_reference_row = $config['floor_cultivation_stock']['base_row'];
        $current_row = $current_row + 2;
      @endphp
    @endforeach

    <tr>
      <td></td>
      <td rowspan="4" align="center" valign="middle" style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};">
        {{ __('view.master.factory_species.seeding') }}{{ __('view.global.amount') }}
      </td>
      <th style="border-bottom: {{ $border }}; border-left: {{ $border }};">
        {{ __('view.master.factory_species.seeding') }}{{ __('view.master.factory_panels.tray') }}
      </th>
      @php
        $current_column = $config['cultivation_simulation']['base_column'];
      @endphp
      @foreach ($seeding_plans as $sp)
        <td style="border-bottom: {{ $border }};
          @if ($loop->first)
          border-left: {{ $border_double }};
          @else
          border-left: {{ $border_bold }};
          @endif"></td>
        <td style="border-bottom: {{ $border }}; border-left: {{ $border }};"></td>
        <td align="center" style="background-color: #FAC559; border-bottom: {{ $border }}; border-left: {{ $border }};" data-format="#,##0">
          {{ $sp->tray }}
        </td>
        @php
          $current_column = $current_column + 3;
        @endphp
      @endforeach
      <td style="background-color: #FAC559; border-bottom: {{ $border }}; border-left: {{ $border_bold }};">
        ={{ implode('+', array_map(function ($idx) use ($current_column, $current_row) {
          return get_excel_column_str($current_column - ($idx * 3) + 2).$current_row;
        }, range(count($working_dates), 1))) }}
      </td>
      <td style="background-color: #FAC559; border-bottom: {{ $border }}; border-left: {{ $border_bold }};">
        {{ $seeding_stage->number_of_holes }}
      </td>
      <td style="background-color: #ffc0cb; border-right: {{ $border_bold }}; border-bottom: {{ $border }}; border-left: {{ $border }};" data-format="#,##0">
        ={{ get_excel_column_str($current_column).($current_row) }}*{{ get_excel_column_str($current_column + 1).($current_row) }}
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td style="border-bottom: {{ $border }}; border-left: {{ $border }};">{{ __('view.master.factory_species.seed') }}{{ __('view.global.amount') }}</td>
      @foreach ($seeding_plans as $sp)
      <td style="border-bottom: {{ $border }};
        @if ($loop->first)
        border-left: {{ $border_double }};
        @else
        border-left: {{ $border_bold }};
        @endif"></td>
      <td style="border-bottom: {{ $border }}; border-left: {{ $border }};"></td>
      <td align="center" style="background-color: #FFC0CB; border-bottom: {{ $border }}; border-left: {{ $border }};" data-format="#,##0">
        {{ $sp->seed }}
      </td>
      @endforeach
      <td style="border-bottom: {{ $border }}; border-left: {{ $border_bold }};"></td>
      <td style="border-bottom: {{ $border }}; border-left: {{ $border_bold }};"></td>
      <td style="border-right: {{ $border_bold }}; border-bottom: {{ $border }}; border-left: {{ $border }};"></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td style="border-bottom: {{ $border }}; border-left: {{ $border }};">
        {{ __('view.master.factory_species.seeding') }}{{ __('view.global.day_of_the_week') }}
      </td>
      @foreach ($seeding_plans as $sp)
      <td style="border-bottom: {{ $border }};
        @if ($loop->first)
        border-left: {{ $border_double }};
        @else
        border-left: {{ $border_bold }};
        @endif"></td>
      <td style="border-bottom: {{ $border }}; border-left: {{ $border }};"></td>
      <td align="center" style="border-bottom: {{ $border }}; border-left: {{ $border }};">
        {{ $sp->seeding_date->dayOfWeekJa() }}
      </td>
      @endforeach
      <td style="border-bottom: {{ $border }}; border-left: {{ $border_bold }};"></td>
      <td style="border-bottom: {{ $border }}; border-left: {{ $border_bold }};"></td>
      <td style="border-right: {{ $border_bold }}; border-bottom: {{ $border }}; border-left: {{ $border }};"></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border }};">
        {{ __('view.master.factory_species.seeding') }}{{ __('view.global.date_term') }}
      </td>
      @foreach ($seeding_plans as $sp)
      <td style="border-bottom: {{ $border_bold }};
        @if ($loop->first)
        border-left: {{ $border_double }};
        @else
        border-left: {{ $border_bold }};
        @endif"></td>
      <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border }};"></td>
      <td align="center" style="border-bottom: {{ $border_bold }}; border-left: {{ $border }};">
        {{ $sp->seeding_date->diffInDays($sp->working_date) }}
      </td>
      @endforeach
      <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};"></td>
      <td style="border-bottom: {{ $border_bold }}; border-left: {{ $border_bold }};"></td>
      <td style="border-right: {{ $border_bold }}; border-bottom: {{ $border_bold }}; border-left: {{ $border }};"></td>
    </tr>
  </table>
</body>
</html>
