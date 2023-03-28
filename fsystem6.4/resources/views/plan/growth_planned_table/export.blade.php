<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
</head>
<body>
  @php ($border = '1px solid #000000')
  @php ($border_bold = '2px medium #000000')
  @php ($border_dashed = '1px dashed #000000')
  @php ($border_dotted = '1px dotted #000000')
  @php ($border_double = '1px double #000000')
  @php ($weight_color = '#1F49A0')
  @php ($ttl_color = '#70307D')
  @php ($excel_row = 0)
  @php ($weight_rows = [])
  @php ($seeding_rows = [])
  @php ($porting_rows = [])
  @php ($planting_rows = [])
  @php ($harvesting_rows = [])
  <table>
    <thead>
      <tr>
        @php ($excel_row++)
        <td colspan="4" align="left">
          <h3>{{ __('view.plan.growth_planned_table.index') }}&nbsp;{{ $factory->factory_abbreviation }}</h3>
        </td>
      </tr>
    </thead>
    <tbody>
      <tr>
        @php ($excel_row++)
        <td align="center" style="font-size:22; color:#FF0000; background-color:#FFFF00;">
          {{ __('view.plan.growth_planned_table.title') }}
        </td>
        <td></td>
        <td></td>
        <td align="left" width="15" style="border-top:{{ $border_bold }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
          {{ __('view.global.day') }}
        </td>
        @foreach ($working_dates as $wd)
        <td align="right" width="11" style="border-top:{{ $border_bold }};
          @if ($loop->last)
            border-right:{{ $border_bold }};
          @endif">
          {{ $wd->format('Y/m/d') }}
        </td>
        @endforeach
      </tr>
      <tr>
        @php ($excel_row++)
        <td></td>
        <td></td>
        <td></td>
        <td align="left" style="border-bottom:{{ $border_bold }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
          {{ __('view.global.day_of_the_week') }}
        </td>
        @foreach ($working_dates as $wd)
        <td align="center" style="border-bottom:{{ $border_bold }};
          @if ($loop->last)
            border-right:{{ $border_bold }};
          @endif
          color:{{ $wd->getDayOfWeekColor() }}">
          （{{ $wd->dayOfWeekJa() }}）
        </td>
        @endforeach
      </tr>

      @foreach ($factory_species_list->groupBySpecies() as $grouped_per_species)
        <tr>
          @php ($excel_row++)
          <th align="left" colspan="4" style="background-color:#92cddc; border:{{ $border_bold }};">
            {{ $grouped_per_species->first()->species->species_name }}
          </th>
          <td colspan="{{ count($working_dates) }}" style="background-color:#92cddc;border:{{ $border_bold }};"></td>
        </tr>
        @foreach ($grouped_per_species as $fs)
          <tr>
            @php ($excel_row++)
            <td height="30" rowspan="{{ (($fs->factory_growing_stages->count() + 1) * 2) + 2 }}" align="right" valign="middle" style="border-bottom:{{ $border_bold }}; background-color:#D9D9D9;" data-format='#,##0"g"'>
              {{ $fs->weight }}
            </td>
            <th colspan="3" align="center" valign="middle" style="border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
              {{ $fs->factory_species_name }}
            </th>
            @foreach ($working_dates as $wd)
              <td
                style="
                @if ($loop->last)
                  border-right:{{ $border_bold }};
                @else
                  border-right:{{ $border_dashed }};
                @endif">
              </td>
            @endforeach
          </tr>
          <tr>
            @php ($excel_row++)
            @php ($weight_rows[] = $excel_row)
            <td></td>
            <td height="30" width="11" align="left" valign="middle" style="border-top:{{ $border_double }}; border-left:{{ $border_bold }};">
              {{ __('view.master.factory_species.stage') }}
            </td>
            <td width="6" align="left" valign="middle" style="border-top:{{ $border_double }}; border-left:{{ $border_dashed }};">
              {{ __('view.master.factory_panels.number_of_holes') }}
            </td>
            <td width="18" align="left" valign="middle" style="border-top:{{ $border_double }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }}; color:{{ $weight_color }}; font-size:12; font-weight:bold;">
              {{ __('view.master.factory_species.harvesting') }}{{ __('view.global.weight') }}
            </td>
            @foreach ($working_dates as $wd)
              <th align="center" valign="middle" style="border-top:{{ $border_double }}; color:{{ $weight_color }};
                @if ($loop->last)
                  border-right:{{ $border_bold }};
                @else
                  border-right:{{ $border_dashed }};
                @endif" data-format='#,##0," kg"'>
                @if ($wd->isWorkingDay($factory))
                ={{
                  get_excel_column_str($loop->iteration + 4).($excel_row + ($fs->factory_growing_stages->count() * 2) + 1)
                  .'*'
                  .'$'.get_excel_column_str(1).'$'.($excel_row - 1)
                }}
                @endif
              </th>
            @endforeach
          </tr>
          <tr>
            @php ($excel_row++)
            <td></td>
            <td rowspan="2" align="left" valign="middle" style="border-top:{{ $border_double }};; border-left:{{ $border_bold }};">
              {{ $fs->seeding_stage->growing_stage_name }}
            </td>
            <td rowspan="2" align="right" valign="middle" style="border-top:{{ $border_double }}; border-left:{{ $border_dashed }};">
              {{ $fs->seeding_stage->number_of_holes }}
            </td>
            <td align="left" style="border-top:{{ $border_double }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
              {{ __('view.plan.growth_planned_table.stock') }}
            </td>
            @foreach ($working_dates as $wd)
              <td style="border-top:{{ $border_double }};
                @if ($loop->last)
                  border-right:{{ $border_bold }};
                @else
                  border-right:{{ $border_dashed }};
                @endif" data-format="#,##0">
                @if ($wd->isWorkingDay($factory))
                  ={{ get_excel_column_str($loop->iteration + 4).($excel_row + 1).'*'.'$'.get_excel_column_str(3).'$'.($excel_row) }}
                @endif
              </td>
            @endforeach
          </tr>
          <tr>
            @php ($excel_row++)
            @php ($seeding_rows[] = $excel_row)
            <td></td>
            <td></td>
            <td></td>
            <td align="left" style="border-top:{{ $border_dotted }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
              {{ __('view.plan.growth_planned_table.panel') }}
            </td>
            @foreach ($working_dates as $wd)
              <td style="border-top:{{ $border_dotted }};
                @if ($loop->last)
                  border-right:{{ $border_bold }};
                @else
                  border-right:{{ $border_dashed }};
                @endif">
                @if ($wd->isWorkingDay($factory))
                  {{ $fs->seeding_stage->tray_count_list->filterByDate($wd)->tray_count ?? '' }}
                @endif
              </td>
            @endforeach
          </tr>
          @foreach ($fs->growing_stages as $fgs)
            <tr>
              @php ($excel_row++)
              <td></td>
              <td rowspan="2" align="left" valign="middle" style="border-left:{{ $border_bold }}; border-top:{{ $border_dashed }};">
                {{ $fgs->growing_stage_name }}
              </td>
              <td rowspan="2" align="right" valign="middle" style="border-left:{{ $border_dashed }}; border-top:{{ $border_dashed }};">
                {{ $fgs->number_of_holes }}
              </td>
              <td align="left" style="border-right:{{ $border_bold }}; border-left:{{ $border_bold }}; border-top:{{ $border_dashed }};">
                {{ __('view.plan.growth_planned_table.stock') }}
              </td>
              @foreach ($working_dates as $wd)
                <td style="border-top:{{ $border_dashed }};
                  @if ($loop->last)
                    border-right:{{ $border_bold }};
                  @else
                    border-right:{{ $border_dashed }};
                  @endif" data-format="#,##0">
                  @if ($wd->isWorkingDay($factory))
                    ={{ get_excel_column_str($loop->iteration + 4).($excel_row + 1).'*'.'$'.get_excel_column_str(3).'$'.$excel_row }}
                  @endif
                </td>
              @endforeach
            </tr>
            <tr>
              @php ($excel_row++)
              @if ($fgs->growing_stage === $growing_stage::PORTING)
                @php ($porting_rows[$fgs->sequence_number][] = $excel_row)
              @endif
              @if ($fgs->growing_stage == $growing_stage::PLANTING)
                @php ($planting_rows[] = $excel_row)
              @endif
              <td></td>
              <td></td>
              <td></td>
              <td align="left" style="border-top:{{ $border_dotted }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
                {{ __('view.plan.growth_planned_table.panel') }}
              </td>
              @foreach ($working_dates as $wd)
                <td style="border-top:{{ $border_dotted }};
                  @if ($loop->last)
                    border-right:{{ $border_bold }};
                  @else
                    border-right:{{ $border_dashed }};
                  @endif">
                  @if ($wd->isWorkingDay($factory))
                    {{ $fgs->panel_count_list->filterByDate($wd)->panel_count ?? '' }}
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
          <tr>
            @php ($excel_row++)
            @php ($harvesting_rows[] = $excel_row)
            <td></td>
            <td rowspan="2" align="left" valign="middle" style="border-top:{{ $border_dashed }}; border-left:{{ $border_bold }}; border-bottom:{{ $border_bold }};">
              {{ __('view.master.factory_species.harvesting') }}
            </td>
            <td rowspan="2" align="right" valign="middle" style="border-top:{{ $border_dashed }}; border-left:{{ $border_dashed }}; border-bottom:{{ $border_bold }};">
              {{ $fs->harvesting_stage->number_of_holes }}
            </td>
            <td align="left" style="border-top:{{ $border_dashed }}; border-left:{{ $border_bold }}; border-right:{{ $border_bold }};">
              {{ __('view.plan.growth_planned_table.stock') }}
            </td>
            @foreach ($working_dates as $wd)
              <td style="border-top:{{ $border_dashed }};
                @if ($loop->last)
                  border-right:{{ $border_bold }};
                @else
                  border-right:{{ $border_dashed }};
                @endif" data-format="#,##0">
                @if ($wd->isWorkingDay($factory))
                  ={{ get_excel_column_str($loop->iteration + 4).($excel_row + 1).'*'.'$'.get_excel_column_str(3).'$'.$excel_row }}
                @endif
              </td>
            @endforeach
          </tr>
          <tr>
            @php ($excel_row++)
            <td></td>
            <td></td>
            <td></td>
            <td align="left" style="border-top:{{ $border_dotted }}; border-left:{{ $border_bold }}; border-bottom:{{ $border_bold }}; border-right:{{ $border_bold }};">
              {{ __('view.plan.growth_planned_table.panel') }}
            </td>
            @foreach ($working_dates as $wd)
            <td style="border-top:{{ $border_dotted }}; border-bottom:{{ $border_bold }};
              @if ($loop->last)
                border-right:{{ $border_bold }};
              @else
                border-right:{{ $border_dashed }};
              @endif">
              @if ($wd->isWorkingDay($factory))
                {{ $fs->harvesting_stage->panel_count_list->filterByDate($wd)->panel_count ?? '' }}
              @endif
            </td>
            @endforeach
          </tr>
        @endforeach
      @endforeach

      <tr>
        @php ($excel_row++)
        <th colspan="2" height="30" align="left" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $weight_color }};">
          {{ __('view.plan.growth_planned_table.ttl_weight') }}
        </th>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        @foreach ($working_dates as $wd)
          <td align="right" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $weight_color }}; font-size:12; font-weight:bold;" data-format='#,##0," kg"'>
            @if ($wd->isWorkingDay($factory) && count($weight_rows) !== 0)
              =SUM({{ implode(',', array_map(function ($row) use ($loop) {
                return get_excel_column_str($loop->iteration + 4).$row;
              }, $weight_rows)) }})
            @endif
          </td>
        @endforeach
      </tr>
      <tr>
        @php ($excel_row++)
        <th colspan="2" height="30" align="left" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }};">
          {{ __('view.plan.growth_planned_table.ttl_seeding') }}{{ __('view.plan.growth_planned_table.number_of_sheets') }}
        </th>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        @foreach ($working_dates as $wd)
          <td align="right" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }}; font-size:12; font-weight:bold;" data-format='#,##0" 枚"'>
            @if ($wd->isWorkingDay($factory) && count($seeding_rows) !== 0)
              =SUM({{ implode(',', array_map(function ($row) use ($loop) {
                return get_excel_column_str($loop->iteration + 4).$row;
              }, $seeding_rows)) }})
            @endif
          </td>
        @endforeach
      </tr>
      @foreach ($porting_rows as $rows)
        <tr>
          @php ($excel_row++)
          <th colspan="2" height="30" align="left" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }};">
            {{ __('view.plan.growth_planned_table.ttl_porting') }}{{ $loop->iteration }}{{ __('view.plan.growth_planned_table.number_of_sheets') }}
          </th>
          <td style="border-bottom:{{ $border_dotted }};"></td>
          <td style="border-bottom:{{ $border_dotted }};"></td>
          @foreach ($working_dates as $wd)
            <td align="right" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }}; font-size:12; font-weight:bold;" data-format='#,##0" 枚"'>
              @if ($wd->isWorkingDay($factory) && count($rows) !== 0)
                =SUM({{ implode(',', array_map(function ($row) use ($loop) {
                  return get_excel_column_str($loop->iteration + 4).$row;
                }, $rows)) }})
              @endif
            </td>
          @endforeach
        </tr>
      @endforeach
      <tr>
        @php ($excel_row++)
        <th colspan="2" height="30" align="left" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }};">
          {{ __('view.plan.growth_planned_table.ttl_planting') }}{{ __('view.plan.growth_planned_table.number_of_sheets') }}
        </th>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        @foreach ($working_dates as $wd)
        <td align="right" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }}; font-size:12; font-weight:bold;" data-format='#,##0" 枚"'>
          @if ($wd->isWorkingDay($factory) && count($planting_rows) !== 0)
            =SUM({{ implode(',', array_map(function ($row) use ($loop) {
              return get_excel_column_str($loop->iteration + 4).$row;
            }, $planting_rows)) }})
          @endif
        </td>
        @endforeach
      </tr>
      <tr>
        @php ($excel_row++)
        <th colspan="2" height="30" align="left" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }};">
          {{ __('view.plan.growth_planned_table.ttl_harvesting') }}
        </th>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        <td style="border-bottom:{{ $border_dotted }};"></td>
        @foreach ($working_dates as $wd)
          <td align="right" valign="middle" style="border-bottom:{{ $border_dotted }}; color:{{ $ttl_color }}; font-size:12; font-weight:bold;" data-format='#,###,##0" 株"'>
            @if ($wd->isWorkingDay($factory) && count($harvesting_rows) !== 0)
              =SUM({{ implode(',', array_map(function ($row) use ($loop) {
                return get_excel_column_str($loop->iteration + 4).$row;
              }, $harvesting_rows)) }})
            @endif
          </td>
        @endforeach
      </tr>
    </tbody>
  </table>
</body>
</html>
