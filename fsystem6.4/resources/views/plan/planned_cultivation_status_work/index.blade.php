@extends('layout')

@section('title')
{{ __('view.plan.planned_cultivation_status_work.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys()) }}">{{ __('view.plan.growth_simulation.add') }}</a>
  </li>
  <li>{{ __('view.plan.planned_cultivation_status_work.index') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-5">
    <a class="btn btn-default btn-lg back-button can-transition" href="{{ route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys()) }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  <div class="col-md-4 col-sm-4 col-xs-7">
    <div class="col-md-4 col-sm-4 col-xs-7">
      <a class="btn btn-default btn-lg pull-right" href="{{ route('plan.planned_cultivation_status_work.export', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}">
        <i class="fa fa-edit"></i> {{ __('view.global.report') }}
      </a>
    </div>
    @if (! $growth_simulation->hasFixed())
    <button id="save-floor-cultivation-stock" class="btn btn-default btn-lg pull-right" type="button">
      <i class="fa fa-save"></i> {{ __('view.global.save') }}
    </button>
    @endif
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <table id="change-screen-table" class="table table-color-bordered">
      <tbody>
        <tr>
          <th>{{ __('view.plan.global.screen_change') }}</th>
          <td class="text-left" colspan="3">
            <a class="can-transition" href="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_cultivation_status_work.sum') }}
            </a>&nbsp;
            <a class="can-transition" href="{{ route('plan.planned_arrangement_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_arrangement_status_work.index') }}
            </a>
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.factories.factory_name') }}</th>
          <td class="text-left">{{ $growth_simulation->factory_species->factory->factory_abbreviation }}</td>
          <th>{{ __('view.master.factory_species.factory_species') }}</th>
          <td class="text-left">{{ $growth_simulation->factory_species->factory_species_name }}</td>
          <th>{{ __('view.plan.growth_simulation.simulation_name') }}</th>
          <td class="text-left">{{ $growth_simulation->simulation_name }}</td>
        </tr>
        <tr>
          <th>
            {{ __('view.plan.global.date_jump') }}
            <span class="required-mark">*</span></th>
          <td>
            <datepicker-ja attr-name="date_jump" date="{{ $simulation_date->format('Y/m/d') }}" :disabled-days-of-week="[0, 2, 3, 4, 5, 6]"></datepicker-ja>
            <button id="switch-date" class="btn btn-default" type="button" value="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), '']) }}">
              <i class="fa fa-location-arrow"></i>&nbsp;{{ __('view.plan.global.jump') }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div id="legend-col" class="col-md-2 col-sm-2 col-xs-2 col-md-offset-6 col-sm-offset-6 col-xs-offset-6">
    <table id="legend-table" class="table table-more-condensed table-color-bordered">
      <tbody>
      @foreach ($growth_simulation->factory_species->factory->factory_beds->groupByFloor()->reverse() as $floor => $grouped)
        <tr>
          <th class="floor background-dark">{{ $floor }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}</th>
          <td>
            <span class="floor_bed_sum" data-floor="{{ $floor }}" data-max-bed="{{ $grouped->count() }}"></span>&nbsp;/
            <span>{{ $grouped->count() }}</span>
          </td>
        <tr>
      @endforeach
      <tbody>
    </table>
  </div>
  <div id="legend-col" class="col-md-4 col-sm-4 col-xs-4">
  @foreach ($growth_simulation->factory_species->factory_growing_stages->exceptSeeding()->groupByGrowingStage() as $grouped)
    <table id="legend-table" class="table table-more-condensed table-color-bordered @if ($loop->last) last-legend @endif">
      <tbody>
        @foreach ($grouped->chunk(2) as $chunked)
        <tr>
          @foreach ($chunked as $fgs)
            <td
              class="rest_bed_count"
              style="background-color:#{{ $fgs->label_color }}"
              data-bed-number="{{ $planned_cultivation_status_works->filterByGrowingStageSequenceNumber($fgs->sequence_number)->first()->bed_number ?? 0 }}"
              data-stage="{{ $fgs->sequence_number}}">
            </td>
            <td class="number-of-holes">{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</td>
            <td>{{ $fgs->growing_stage_name }}</td>
          @endforeach
        </tr>
        @endforeach
        </tr>
      </tbody>
    </table>
  @endforeach
  </div>
</div>

<div class="row" id="display-period-table">
  <div class="col-md-3 col-sm-3 col-xs-3 text-right">
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->subMonth()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->subMonth()->startOfweek()->format('Y-m-d')]) }}">
      <i class="fa fa-angle-double-left"></i>&nbsp;{{ __('view.global.prev_month') }}
    </a>
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->subWeek()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->subWeek()->startOfweek()->format('Y-m-d')]) }}">
      <i class="fa fa-angle-left"></i>&nbsp;{{ __('view.global.prev_week') }}
    </a>
  </div>
  <div id="display-period-col" class="col-md-5 col-sm-5 col-xs-5 text-center">
    {{ $simulation_date->startOfWeek()->formatWithDayOfWeek() }}&nbsp;～&nbsp;{{ $simulation_date->endOfWeek()->formatWithDayOfWeek() }}
  </div>
  <div class="col-md-3 col-sm-3 col-xs-3 text-left">
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->addWeek()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->addWeek()->startOfweek()->format('Y-m-d')]) }}">
      {{ __('view.global.next_week') }}&nbsp;<i class="fa fa-angle-right"></i>
    </a>
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->addMonth()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->addMonth()->startOfweek()->format('Y-m-d')]) }}">
      {{ __('view.global.next_month') }}&nbsp;<i class="fa fa-angle-double-right"></i>
    </a>
  </div>
</div>

<form id="save-floor-cultivation-stock-form" action="{{ route('plan.planned_cultivation_status_work.save', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}" method="POST">
  <div class="row" id="moving-panel-table">
    <div class="col-md-12 col-sm-12 col-xs-12 col-no-padding-right">
      <table class="table panel-wrapper-table header-table">
        <tbody>
          <tr>
            @foreach ($growth_simulation->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
            <td>
              <div class="table-responsive">
                <table class="table table-color-bordered cycle-pattern-table">
                  <colgroup>
                    @for ($i = 0; $i < count($simulatable_dates) + 3; $i++)
                      <col class="table-cols">
                    @endfor
                  </colgroup>
                  <tbody>
                    <tr>
                      <th colspan="{{ count($simulatable_dates) + 3 }}" class="background-dark">{{ $fgs->growing_stage_name }}</th>
                    </tr>
                    <tr class="border-none-top">
                      <th colspan="3" class="border-right-double background-dark"></th>
                      @foreach ($simulatable_dates as $sd)
                      <th>{{ $sd->dayOfWeekJa() }}</th>
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
                          @if ($pcsw = $planned_cultivation_status_works->findByGrowingStageSequenceNumberAndDayOfTheWeek($fgs->sequence_number, $fspi->day_of_the_week))
                            <input
                              class="form-control text-right moving_panel_count_pattern ime-disabled stage-{{ $fgs->sequence_number }} week-{{ $fspi->day_of_the_week }} pattern-{{ $loop->parent->iteration }}"
                              maxlength="2"
                              name="moving_panel_count_pattern[{{ $fgs->sequence_number }}][{{ $fspi->day_of_the_week }}][{{ $loop->parent->iteration }}]"
                              value="{{ $pcsw->getMovingPanelCountPattern($loop->parent->iteration) ?: $fspi->number_of_panels }}"
                              @if ($growth_simulation->hasFixed()) disabled @endif>
                          @else
                            <input class="form-control text-right" maxlength="2" disabled>
                          @endif
                        </td>
                      @endforeach
                    </tr>
                    @if ($loop->last)
                      @for ($i = $loop->iteration; $i < $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count(); $i++)
                      <tr>
                        <th>&nbsp;</th>
                        <td class="border-right-double"></td>
                        @foreach ($simulatable_dates as $sd)
                          <td></td>
                        @endforeach
                      </tr>
                      @endfor
                    @endif
                    @endforeach
                    <tr>
                      <th>{{ __('view.global.sum') }}</th>
                      <td class="bed_count_sum border-right-double text-right" data-stage="{{ $fgs->sequence_number }}"></td>
                      @foreach ($simulatable_dates as $sd)
                      <td class="moving_panel_count_pattern_sum text-right" data-stage="{{ $fgs->sequence_number }}" data-week="{{ $sd->format('w') }}"></td>
                      @endforeach
                    </tr>
                    <tr class="border-top-bold">
                      <th colspan="2" class="background-dark">{{ __('view.global.total') }}</th>
                      <td class="bed_count_sum  border-right-double text-right" data-stage="{{ $fgs->sequence_number }}"></td>
                      @foreach ($simulatable_dates as $sd)
                      <td class="cultivation_stock_sum text-right" data-stage="{{ $fgs->sequence_number }}" data-week="{{ $sd->format('w') }}" ></td>
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

  <div class="row" id="moving-bed-table">
    <div class="col-md-12 col-sm-12 col-xs-12 col-no-padding-right">
      <table class="table panel-wrapper-table">
        <tbody>
          <tr>
            @foreach ($growth_simulation->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
            <td>
              <div class="table-responsive">
                <table class="table table-color-bordered cycle-pattern-table">
                  <colgroup>
                    @for ($i = 0; $i < count($simulatable_dates) + 3; $i++)
                      <col class="table-cols">
                    @endfor
                  </colgroup>
                  <tbody>
                    @foreach ($growth_simulation->factory_species->factory->factory_beds->groupByFloor()->reverse() as $floor => $grouped)
                      @foreach ($fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern() as $pattern => $grouped_patterns)
                      <tr @if ($loop->first) class="border-top-bold" @endif>
                        @if ($loop->first)
                        <th rowspan="{{ $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count() + 1 }}" class="background-dark">
                          {{ $floor }}{{ __('view.master.factory_beds.floor_en_abbreviation') }}
                        </th>
                        @endif
                        <th>{{ $pattern }}</th>
                        <td class="border-right-double text-right" style="background-color:#{{ $fgs->label_color }}">
                        @if ($planned_cultivation_status_works->filterByGrowingStageSequenceNumber($fgs->sequence_number)->isNotEmpty())
                          <input
                            class="form-control text-right moving_bed_count_floor_pattern ime-disabled stage-{{ $fgs->sequence_number }} floor-{{ $floor }} pattern-{{ $loop->iteration }}"
                            maxlength="3"
                            name="moving_bed_count_floor_pattern[{{ $fgs->sequence_number }}][{{ $floor }}][{{ $loop->iteration }}]"
                            style="background-color:#{{ $fgs->label_color }};"
                            value="{{ $planned_cultivation_status_works->filterByGrowingStageSequenceNumber($fgs->sequence_number)->first()->getMovingBedCountFloorPattern($floor, $loop->iteration) }}"
                            @if ($growth_simulation->hasFixed()) disabled @endif>
                        @else
                          <input class="form-control text-right" maxlength="3" style="background-color:#{{ $fgs->label_color }};" disabled>
                        @endif
                        </td>
                        @foreach ($simulatable_dates as $sd)
                        <td
                          class="floor_cultivation_stock stage-{{ $fgs->sequence_number }} floor-{{ $floor }} week-{{ $sd->format('w') }} pattern-{{ $loop->parent->iteration }} text-right"
                          data-stage="{{ $fgs->sequence_number }}"
                          data-floor="{{ $floor }}"
                          data-week="{{ $sd->format('w') }}"
                          data-pattern="{{ $loop->parent->iteration }}"></td>
                        @endforeach
                      </tr>
                      @if ($loop->last)
                        @for ($i = $loop->iteration; $i < $fgs->factory_cycle_pattern->factory_cycle_pattern_items->groupByPattern()->count(); $i++)
                        <tr>
                          <th>&nbsp;</th>
                          <td class="text-right border-right-double"></td>
                          @foreach ($simulatable_dates as $sd)
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
                        @foreach ($simulatable_dates as $sd)
                          <td
                            class="floor_cultivation_stock_sum stage-{{ $fgs->sequence_number }} floor-{{ $floor }} week-{{ $sd->format('w') }} text-right"
                            data-stage="{{ $fgs->sequence_number }}"
                            data-floor="{{ $floor }}"
                            data-week="{{ $sd->format('w') }}"></td>
                        @endforeach
                      </tr>
                    @endforeach
                    <tr class="border-top-bold">
                      <th colspan="2" class="background-dark">{{ __('view.global.total') }}</th>
                      <td class="bed_count_sum border-right-double text-right" data-stage="{{ $fgs->sequence_number }}"></td>
                      @foreach ($simulatable_dates as $sd)
                      <td class="cultivation_stock_sum text-right" data-stage="{{ $fgs->sequence_number }}" data-week="{{ $sd->format('w') }}" ></td>
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
    {{ method_field('PATCH') }}
    {{ csrf_field() }}
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

    $('#switch-date').click(function () {
      if ($('#save-floor-cultivation-stock-form').data('changed') &&
        ! confirm('保存されていない情報があります。画面を切り替えてよろしいですか？')) {
        return
      }

      location.href = $(this).val() + '/' + $(this).prev().attr('value').replace(/\//g, '-')
    })

    $('.can-transition').click(function (e) {
      if ($('#save-floor-cultivation-stock-form').data('changed') &&
        ! confirm('保存されていない情報があります。画面を切り替えてよろしいですか？')) {
        e.preventDefault()
        return false
      }

      return true
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

      $('.moving_panel_count_pattern').change(function () {
        $('#save-floor-cultivation-stock-form').data('changed', true)
        reload_cultivation_stock()
      })
      $('.moving_bed_count_floor_pattern').change(function () {
        $('#save-floor-cultivation-stock-form').data('changed', true)
        reload_cultivation_stock()
      })

      reload_cultivation_stock()
    }

    $('#save-floor-cultivation-stock').click(function () {
      $(this).prop('disabled', true)

      var over_bed_count = false
      $('.floor_bed_sum').each(function() {
        var sum_elm = $('.moving_bed_count_floor_pattern' + '.floor-' + $(this).data('floor'))
        var sum_calc = 0
        for (var i = 0; i < sum_elm.length; i++) {
          var add = parseInt(sum_elm.eq(i).val(), 10)
          if (isFinite(add)) {
            sum_calc += add
          }
        }
        if (sum_calc > $(this).data('max-bed')) {
          $(this).parents('td').addClass('has-error')
          over_bed_count = true
        }
      })

      if (over_bed_count) {
        alert('移動ベッド数のフロアごとの合計は、フロアのベット数以下の数字を指定してください。')
        $(this).prop('disabled', false)
        return
      }

      if (! confirm('表示されている日付以降を保存します。よろしいですか？')){
        $(this).prop('disabled', false)
      }

      $('.alert').remove()
      $('#save-floor-cultivation-stock-form').submit()
    })
  })
  </script>
@endsection
