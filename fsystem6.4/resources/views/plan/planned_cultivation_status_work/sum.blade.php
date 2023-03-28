@extends('layout')

@section('title')
{{ __('view.plan.planned_cultivation_status_work.sum') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys()) }}">{{ __('view.plan.growth_simulation.add') }}</a>
  </li>
  <li>{{ __('view.plan.planned_cultivation_status_work.sum') }}</li>
@endsection

@section('styles')
  @parent

  <style>
    table#cultivation-states-sum-table {
      border-style: solid;
      border-width: 2px;
    }
    td.text-danger {
      color: #ff0000;
    }
    td.sum-of-quantity {
      background-color: #d7e4bd;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-5">
    <a class="btn btn-default btn-lg back-button" href="{{ route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys()) }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  <div class="col-md-4 col-sm-4 col-xs-7">
    <a class="btn btn-default btn-lg pull-right" href="{{ route('plan.planned_cultivation_status_work.export', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}">
      <i class="fa fa-edit"></i> {{ __('view.global.report') }}
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <table class="table table-color-bordered">
      <tbody>
        <tr>
          <th>{{ __('view.plan.global.screen_change') }}</th>
          <td class="text-left" colspan="3">
            <a class="can-transition" href="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_cultivation_status_work.index') }}
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

<div class="row" id="display-period-table">
  <div class="col-md-3 col-sm-3 col-xs-3 text-right">
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->subMonth()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->subMonth()->startOfweek()->format('Y-m-d')]) }}">
      <i class="fa fa-angle-double-left"></i>&nbsp;{{ __('view.global.prev_month') }}
    </a>
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->subWeek()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->subWeek()->startOfweek()->format('Y-m-d')]) }}">
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
      href="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->addWeek()->startOfweek()->format('Y-m-d')]) }}">
      {{ __('view.global.next_week') }}&nbsp;<i class="fa fa-angle-right"></i>
    </a>
    <a class="btn btn-default can-transition
      @if (! $growth_simulation->canSimulateOnTheDate($simulation_date->addMonth()->startOfweek()))
      disabled
      @endif"
      href="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->addMonth()->startOfweek()->format('Y-m-d')]) }}">
      {{ __('view.global.next_month') }}&nbsp;<i class="fa fa-angle-double-right"></i>
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <table id="cultivation-states-sum-table" class="table table-color-bordered">
      <tbody>
        <tr>
          <th rowspan="2">{{ __('view.master.factory_beds.floor') }}</th>
          <th rowspan="2">{{ __('view.master.factory_species.growing_stage') }}</th>
          @foreach ($simulatable_dates as $sd)
          <th colspan="3">{{ $sd->dayOfWeekJa() }}</th>
          @endforeach
        </tr>
        <tr>
          @foreach ($simulatable_dates as $sd)
          <th>{{ __('view.plan.planned_cultivation_status_work.growing_stock_quantity') }}</th>
          <th>{{ __('view.plan.planned_cultivation_status_work.panel_quantity') }}</th>
          <th>{{ __('view.plan.planned_cultivation_status_work.excess_or_deficiency') }}</th>
          @endforeach
        </tr>
        @foreach (range($growth_simulation->factory_species->factory->number_of_floors, 1) as $floor)
          @foreach ($growth_simulation->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
            <tr>
              @if ($loop->first)
              <th rowspan="{{ $loop->count }}">{{ $floor }}F</th>
              @endif
              <th class="text-left">{{ $fgs->growing_stage_name }}:&nbsp;{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</th>
              @foreach ($simulatable_dates as $sd)
                @php
                  $pcsw = $planned_cultivation_status_works->findByDateAndGrowingStageSequenceNumber($sd, $fgs->sequence_number)
                @endphp
                <td class="text-right">
                  {{ number_format(optional($pcsw)->getGrowingStockQuantityByFloor($floor) ?: 0) }}
                </td>
                <td class="text-right">
                  {{ number_format(optional($pcsw)->getPanelQuantityByFloor($floor) ?: 0) }}{{ __('view.master.factory_panels.panel') }}
                </td>
                @if ($loop->parent->last)
                <td></td>
                @else
                  @php
                    $excess_or_deficiency = $planned_cultivation_status_works->getExcessOrDeficiencyByFloor($sd, $fgs->sequence_number, $floor)
                  @endphp
                  <td class="text-right
                    @if ($excess_or_deficiency < 0)
                    text-danger
                    @endif">
                    {{ number_format($excess_or_deficiency) }}
                  </td>
                @endif
              @endforeach
            </tr>
          @endforeach
        @endforeach
        @foreach ($growth_simulation->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
        <tr>
          @if ($loop->first)
          <th rowspan="{{ $loop->count }}">{{ __('view.global.total') }}</th>
          @endif
          <th class="text-left">{{ $fgs->growing_stage_name }}:&nbsp;{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</th>
          @foreach ($simulatable_dates as $sd)
            @php
              $pcsw = $planned_cultivation_status_works->findByDateAndGrowingStageSequenceNumber($sd, $fgs->sequence_number)
            @endphp
            <td class="text-right sum-of-quantity">
              {{ number_format(optional($pcsw)->getSumOfGrowingStockQuantity($floor) ?: 0) }}
            </td>
            <td class="text-right sum-of-quantity">
              {{ number_format(optional($pcsw)->getSumOfPanelQuantity($floor) ?: 0) }}{{ __('view.master.factory_panels.panel') }}
            </td>
            @if ($loop->parent->last)
            <td class="sum-of-quantity"></td>
            @else
            @php
              $excess_or_deficiency = $planned_cultivation_status_works->getSumOfExcessOrDeficiency($sd, $fgs->sequence_number, $growth_simulation->factory_species->factory->number_of_floors)
            @endphp
            <td class="text-right sum-of-quantity
              @if ($excess_or_deficiency < 0)
              text-danger
              @endif">
              {{ number_format($excess_or_deficiency) }}
            </td>
            @endif
          @endforeach
        </tr>
        @endforeach
        <tr>
          <th></th>
          <th class="text-left">
          @php
            $seeding_stage = $growth_simulation->factory_species->factory_growing_stages->first()
          @endphp
          {{ __('view.master.factory_species.seeding') }}:&nbsp;{{ $seeding_stage->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</th>
          @foreach ($simulatable_dates as $sd)
            @php
              $seeding_simulations = $growth_simulation->growth_simulation_items->filterByGrwoingStageAndDate($seeding_stage, $sd)
            @endphp
            <td class="text-right">{{ number_format($seeding_simulations->sum('stock_number')) }}</td>
            <td class="text-right">
              {{ number_format($seeding_simulations->sum('panel_number')) }}{{ __('view.master.factory_panels.tray') }}
            </td>
            <td></td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
  $(function () {
    $('#switch-date').click(function () {
      location.href = $(this).val() + '/' + $(this).prev().attr('value').replace(/\//g, '-') + '/sum'
    })
  })
  </script>
@endsection
