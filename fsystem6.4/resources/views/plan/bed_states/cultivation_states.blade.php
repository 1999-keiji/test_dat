@extends('layout')

@section('title')
{{ __('view.plan.planned_cultivation_status_work.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.bed_states.index') }}">{{ __('view.plan.bed_states.index') }}</a>
  </li>
  <li>{{ __('view.plan.planned_cultivation_status_work.index') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-5">
    <a class="btn btn-default btn-lg back-button" href="{{ route('plan.bed_states.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  <div class="col-md-4 col-sm-4 col-xs-7">
    <a class="btn btn-default btn-lg pull-right" href="{{ route('plan.bed_states.cultivation_states.export', $bed_state->getJoinedPrimaryKeys()) }}">
      <i class="fa fa-edit"></i> {{ __('view.global.report') }}
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-8 col-sm-10 col-xs-12 col-md-offset-1 col-sm-ofset-1">
    <table class="table table-color-bordered">
      <tbody>
        <tr>
          <th>{{ __('view.plan.global.screen_change') }}</th>
          <td class="text-left" colspan="2">
            <a href="{{ route('plan.bed_states.cultivation_states.sum', $bed_state->getJoinedPrimaryKeys()) }}">
              {{ __('view.plan.planned_cultivation_status_work.sum') }}
            </a>&nbsp;
            <a href="{{ route('plan.bed_states.arrangement_states.index', [$bed_state->getJoinedPrimaryKeys(), $bed_state->getStartOfWeek()->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_arrangement_status_work.index') }}
            </a>
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.factories.factory_name') }}</th>
          <td class="text-left">{{ $bed_state->factory_species->factory->factory_abbreviation }}</td>
          <th>{{ __('view.master.factory_species.factory_species') }}</th>
          <td class="text-left">{{ $bed_state->factory_species->factory_species_name }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div id="display-period-col" class="col-md-5 col-sm-5 col-xs-5 col-md-offset-3 col-sm-offset-3 col-xs-offset-3 text-center">
    {{ $bed_state->getStartOfWeek()->formatWithDayOfWeek() }}&nbsp;～&nbsp;{{ $bed_state->getStartOfWeek()->endOfWeek()->formatWithDayOfWeek() }}
  </div>
</div>

<form id="save-floor-cultivation-stock-form" action="#">
  <div id="moving-panel-table" class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 col-no-padding-right">
      <table class="table panel-wrapper-table header-table">
        <tbody>
          <tr>
            @foreach ($bed_state->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
            <td>
              <div class="table-responsive">
                <table class="table table-color-bordered cycle-pattern-table">
                  <colgroup>
                    @for ($i = 0; $i < count($working_dates) + 3; $i++)
                      <col class="table-cols">
                    @endfor
                  </colgroup>
                  <tbody>
                    <tr>
                      <th colspan="{{ count($working_dates) + 3 }}" class="background-dark">{{ $fgs->growing_stage_name }}</th>
                    </tr>
                    <tr class="border-none-top">
                      <th colspan="3" class="border-right-double background-dark"></th>
                      @foreach ($working_dates as $wd)
                      <th>{{ $wd->dayOfWeekJa() }}</th>
                      @endforeach
                    </tr>
                    @foreach ($fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern() as $pattern => $grouped_patterns)
                    <tr
                      @if ($loop->first)
                      class="border-top-double"
                      @endif>
                      @if ($loop->first)
                      <th rowspan="{{ $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count() + 1 }}" class="background-dark">
                        <span class="number_of_holes stage-{{$fgs->sequence_number}}">{{ $fgs->number_of_holes }}</span>{{ __('view.master.factory_panels.hole') }}
                      </th>
                      @endif
                      <th>{{ $pattern }}</th>
                      <td class="moving_bed_count_pttern_sum border-right-double text-right" data-stage="{{$fgs->sequence_number}}" data-pattern="{{ $loop->iteration }}"></td>
                      @foreach ($grouped_patterns as $fspi)
                        <td>
                          <input
                            class="form-control text-right moving_panel_count_pattern ime-disabled stage-{{ $fgs->sequence_number }} week-{{ $fspi->day_of_the_week }} pattern-{{ $loop->parent->iteration }}"
                            value="{{ $fspi->number_of_panels  }}"
                            disabled>
                        </td>
                      @endforeach
                    </tr>
                    @if ($loop->last)
                      @for ($i = $loop->iteration; $i < $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count(); $i++)
                      <tr>
                        <th>&nbsp;</th>
                        <td class="border-right-double"></td>
                        @foreach ($working_dates as $wd)
                          <td></td>
                        @endforeach
                      </tr>
                      @endfor
                    @endif
                    @endforeach
                    <tr>
                      <th>{{ __('view.global.sum') }}</th>
                      <td class="bed_count_sum border-right-double text-right" data-stage="{{ $fgs->sequence_number }}"></td>
                      @foreach ($working_dates as $wd)
                      <td class="moving_panel_count_pattern_sum text-right" data-stage="{{ $fgs->sequence_number }}" data-week="{{ $wd->format('w') }}"></td>
                      @endforeach
                    </tr>
                    <tr class="border-top-bold">
                      <th colspan="2" class="background-dark">{{ __('view.global.total') }}</th>
                      <td class="bed_count_sum  border-right-double text-right" data-stage="{{ $fgs->sequence_number }}"></td>
                      @foreach ($working_dates as $wd)
                      <td class="cultivation_stock_sum text-right" data-stage="{{ $fgs->sequence_number }}" data-week="{{ $wd->format('w') }}" ></td>
                      @endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            </td>
            @endforeach
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="moving-bed-table" class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 col-no-padding-right">
      <table class="table panel-wrapper-table">
        <tbody>
          <tr>
            @foreach ($bed_state->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
            <td>
              <div class="table-responsive">
                <table class="table table-color-bordered cycle-pattern-table">
                  <colgroup>
                    @for ($i = 0; $i < count($working_dates) + 3; $i++)
                      <col class="table-cols">
                    @endfor
                  </colgroup>
                  <tbody>
                    @foreach ($bed_state->factory_species->factory->factory_beds->groupByFloor()->reverse() as $floor => $grouped)
                      @foreach ($fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern() as $pattern => $grouped_patterns)
                      <tr @if ($loop->first) class="border-top-bold" @endif>
                        @if ($loop->first)
                        <th rowspan="{{ $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count() + 1 }}" class="background-dark">
                          {{ $floor }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}
                        </th>
                        @endif
                        <th>{{ $pattern }}</th>
                        <td class="border-right-double text-right" style="background-color:#{{ $fgs->label_color }}">
                        @if ($bed_state->cultivation_states->filterByGrowingStageSequenceNumber($fgs->sequence_number)->isNotEmpty())
                          <input
                            class="form-control text-right moving_bed_count_floor_pattern ime-disabled stage-{{ $fgs->sequence_number }} floor-{{ $floor }} pattern-{{ $loop->iteration }}"
                            maxlength="3"
                            style="background-color:#{{ $fgs->label_color }};"
                            value="{{ $bed_state->cultivation_states->filterByGrowingStageSequenceNumber($fgs->sequence_number)->first()->getMovingBedCountFloorPattern($floor, $loop->iteration) }}"
                            disabled>
                        @else
                          <input class="form-control text-right" maxlength="3" style="background-color:#{{ $fgs->label_color }};" disabled>
                        @endif
                        </td>
                        @foreach ($working_dates as $wd)
                        <td
                          class="floor_cultivation_stock stage-{{ $fgs->sequence_number }} floor-{{ $floor }} week-{{ $wd->format('w') }} pattern-{{ $loop->parent->iteration }} text-right"
                          data-stage="{{ $fgs->sequence_number }}"
                          data-floor="{{ $floor }}"
                          data-week="{{ $wd->format('w') }}"
                          data-pattern="{{ $loop->parent->iteration }}"></td>
                        @endforeach
                      </tr>
                      @if ($loop->last)
                        @for ($i = $loop->iteration; $i < $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count(); $i++)
                        <tr>
                          <th>&nbsp;</th>
                          <td class="text-right border-right-double"></td>
                          @foreach ($working_dates as $wd)
                            <td></td>
                          @endforeach
                        </tr>
                        @endfor
                      @endif
                      @endforeach
                      <tr>
                        <th>{{ __('view.global.sum') }}</th>
                        <td
                          class="moving_bed_count_floor_sum border-right-double stage-{{ $fgs->sequence_number }} floor-{{ $floor }} text-right"
                          data-stage="{{ $fgs->sequence_number }}"
                          data-floor="{{ $floor }}"
                          data-max-bed="{{ $grouped->count() }}"></td>
                        @foreach ($working_dates as $wd)
                          <td
                            class="floor_cultivation_stock_sum stage-{{ $fgs->sequence_number }} floor-{{ $floor }} week-{{ $wd->format('w') }} text-right"
                            data-stage="{{ $fgs->sequence_number }}"
                            data-floor="{{ $floor }}"
                            data-week="{{ $wd->format('w') }}"></td>
                        @endforeach
                      </tr>
                    @endforeach
                    <tr class="border-top-bold">
                      <th colspan="2" class="background-dark">{{ __('view.global.total') }}</th>
                      <td class="bed_count_sum border-right-double text-right" data-stage="{{ $fgs->sequence_number }}"></td>
                      @foreach ($working_dates as $wd)
                      <td class="cultivation_stock_sum text-right" data-stage="{{ $fgs->sequence_number }}" data-week="{{ $wd->format('w') }}" ></td>
                      @endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            </td>
          @endforeach
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</form>
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
  $(function () {
    $(window).on('resize', function () {
      var min_height = $('#moving-panel-table').outerHeight(),
        table_height = $(window).height() - ($('#moving-bed-table').offset().top + 20)
      $('#moving-bed-table').outerHeight(Math.max(table_height, min_height))
      $('#moving-panel-table').outerWidth($('#moving-bed-table').get(0).clientWidth)
    }).trigger('resize')

    $('#moving-bed-table').scroll(function() {
      $('#moving-panel-table').scrollLeft($(this).scrollLeft())
    })

    if ($('#save-floor-cultivation-stock-form').length) {
      var reload_cultivation_stock = function () {
        $('.moving_bed_count_pttern_sum').each(function () {
          var stage_class = '.stage-'+$(this).data('stage'),
            pttern_class = '.pattern-'+$(this).data('pattern'),
            sum_elm = $('.moving_bed_count_floor_pattern' + stage_class + pttern_class),
            sum_calc = 0
          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).val(),10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          $(this).text(sum_elm.length > 0 ? sum_calc.toLocaleString() : '')
        })

        $('.moving_panel_count_pattern_sum').each(function () {
          var stage_class = '.stage-' + $(this).data('stage'),
            week_class = '.week-' + $(this).data('week'),
            sum_elm = $('.moving_panel_count_pattern' + stage_class+week_class),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).val(),10)
            if (isFinite(add)) sum_calc += add
          }

          $(this).text(sum_elm.length > 0 ? sum_calc.toLocaleString() : '')
        })

        $('.floor_cultivation_stock').each(function () {
          var stage_class = '.stage-' + $(this).data('stage'),
            floor_class = '.floor-' + $(this).data('floor'),
            week_class = '.week-' + $(this).data('week'),
            pttern_class = '.pattern-' + $(this).data('pattern'),
            bed_count = $('.moving_bed_count_floor_pattern' + stage_class + floor_class + pttern_class).val(),
            panel_count = $('.moving_panel_count_pattern' + stage_class+week_class + pttern_class).val(),
            hole_count = $('.number_of_holes'+stage_class).html().replace(/,/, ''),
            sum_calc = parseInt(bed_count, 10) * parseInt(panel_count, 10) * parseInt(hole_count, 10)

          $(this).text((isFinite(sum_calc) ? sum_calc.toLocaleString() : ''))
        })

        $('.moving_bed_count_floor_sum').each(function () {
          var stage_class = '.stage-' + $(this).data('stage'),
            floor_class = '.floor-' + $(this).data('floor'),
            sum_elm = $('.moving_bed_count_floor_pattern' + stage_class+floor_class),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).val(), 10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          $(this).text(sum_elm.length > 0 ? sum_calc.toLocaleString() : '')
        })

        $('.floor_bed_sum').each(function () {
          var sum_elm = $('.moving_bed_count_floor_pattern' + '.floor-' + $(this).data('floor')),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).val(), 10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          if (sum_calc > $(this).data('max-bed')) {
            $(this).parents('td').addClass('has-error')
          } else {
            $(this).parents('td').removeClass('has-error')
          }

          $(this).text(sum_elm.length > 0 ? sum_calc.toLocaleString() : 0)
        })

        $('.floor_cultivation_stock_sum').each(function () {
          var stage_class = '.stage-' + $(this).data('stage'),
            floor_class = '.floor-' + $(this).data('floor'),
            week_class = '.week-' + $(this).data('week'),
            sum_elm = $('.floor_cultivation_stock' + stage_class + floor_class+week_class),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).html().replace(/,/,''), 10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          $(this).text(sum_elm.length > 0 ? sum_calc.toLocaleString() : '')
        })

        $('.bed_count_sum').each(function () {
          var stage_class = '.stage-'+$(this).data('stage'),
            sum_elm = $('.moving_bed_count_floor_sum' + stage_class),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).html().replace(/,/,''),10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          $(this).text(sum_elm.length >0 ? sum_calc.toLocaleString() : '')
        })

        $('.cultivation_stock_sum').each(function () {
          var stage_class = '.stage-' + $(this).data('stage'),
            week_class = '.week-' + $(this).data('week'),
            sum_elm = $('.floor_cultivation_stock_sum' + stage_class + week_class),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).html().replace(/,/,''), 10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          $(this).text(sum_elm.length >0 ? sum_calc.toLocaleString() : '')
        })

        $('.rest_bed_count').each(function () {
          var bed_number = $(this).data('bed-number'),
            stage_class = '.stage-' + $(this).data('stage'),
            sum_elm = $('.moving_bed_count_floor_pattern' + stage_class),
            sum_calc = 0

          for (var i = 0; i < sum_elm.length; i++) {
            var add = parseInt(sum_elm.eq(i).val(), 10)
            if (isFinite(add)) {
              sum_calc += add
            }
          }

          $(this).text((bed_number - sum_calc).toLocaleString())
        })
      }

      reload_cultivation_stock()
    }
  })
  </script>
@endsection
