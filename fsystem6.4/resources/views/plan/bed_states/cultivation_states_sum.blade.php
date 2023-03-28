@extends('layout')

@section('title')
{{ __('view.plan.planned_cultivation_status_work.sum') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.bed_states.index') }}">{{ __('view.plan.bed_states.index') }}</a>
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
            <a href="{{ route('plan.bed_states.cultivation_states.index', $bed_state->getJoinedPrimaryKeys()) }}">
              {{ __('view.plan.planned_cultivation_status_work.index') }}
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
  <div id="display-period-col" class="col-md-6 col-sm-8 col-xs-10 col-md-offset-3 col-sm-offset-2 col-xs-offset-1 text-center">
    {{ $bed_state->getStartOfWeek()->formatWithDayOfWeek() }}&nbsp;～&nbsp;{{ $bed_state->getStartOfWeek()->endOfWeek()->formatWithDayOfWeek() }}
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <table id="cultivation-states-sum-table"  class="table table-color-bordered">
      <tbody>
        <tr>
          <th rowspan="2">{{ __('view.master.factory_beds.floor') }}</th>
          <th rowspan="2">{{ __('view.master.factory_species.growing_stage') }}</th>
          @foreach ($working_dates as $wd)
          <th colspan="3">{{ $wd->dayOfWeekJa() }}</th>
          @endforeach
        </tr>
        <tr>
          @foreach ($working_dates as $wd)
          <th>{{ __('view.plan.planned_cultivation_status_work.growing_stock_quantity') }}</th>
          <th>{{ __('view.plan.planned_cultivation_status_work.panel_quantity') }}</th>
          <th>{{ __('view.plan.planned_cultivation_status_work.excess_or_deficiency') }}</th>
          @endforeach
        </tr>
        @foreach (range($bed_state->factory_species->factory->number_of_floors, 1) as $floor)
          @foreach ($bed_state->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
            <tr>
              @if ($loop->first)
              <th rowspan="{{ $loop->count }}">{{ $floor }}F</th>
              @endif
              <th class="text-left">{{ $fgs->growing_stage_name }}:&nbsp;{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</th>
              @foreach ($working_dates as $wd)
                @php
                  $cs = $bed_state->cultivation_states->findByWorkingDateAndGrowingStageSequenceNumber($wd, $fgs->sequence_number)
                @endphp
                <td class="text-right">
                  {{ number_format(optional($cs)->getGrowingStockQuantityByFloor($floor) ?: 0) }}
                </td>
                <td class="text-right">
                  {{ number_format(optional($cs)->getPanelQuantityByFloor($floor) ?: 0) }}{{ __('view.master.factory_panels.panel') }}
                </td>
                @if ($loop->parent->last)
                <td></td>
                @else
                  @php
                    $excess_or_deficiency = $bed_state->cultivation_states->getExcessOrDeficiencyByFloor($wd, $fgs->sequence_number, $floor)
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
        @foreach ($bed_state->factory_species->factory_growing_stages->exceptSeeding()->reverse() as $fgs)
        <tr>
          @if ($loop->first)
          <th rowspan="{{ $loop->count }}">{{ __('view.global.total') }}</th>
          @endif
          <th class="text-left">{{ $fgs->growing_stage_name }}:&nbsp;{{ $fgs->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</th>
          @foreach ($working_dates as $wd)
            @php
              $pcsw = $bed_state->cultivation_states->findByWorkingDateAndGrowingStageSequenceNumber($wd, $fgs->sequence_number)
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
              $excess_or_deficiency = $bed_state->cultivation_states->getSumOfExcessOrDeficiency($wd, $fgs->sequence_number, $bed_state->factory_species->factory->number_of_floors)
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
            $seeding_stage = $bed_state->factory_species->factory_growing_stages->first()
          @endphp
          {{ __('view.master.factory_species.seeding') }}:&nbsp;{{ $seeding_stage->number_of_holes }}{{ __('view.master.factory_panels.hole') }}</th>
          @foreach ($working_dates as $idx => $wd)
            <td class="text-right">
              @if ($bed_state->seeding_plans->has($idx))
              {{ number_format($bed_state->seeding_plans[$idx]->number_of_trays * $seeding_stage->number_of_holes) }}
              @endif
            </td>
            <td class="text-right">
              @if ($bed_state->seeding_plans->has($idx))
              {{ number_format($bed_state->seeding_plans[$idx]->number_of_trays) }}{{ __('view.master.factory_panels.tray') }}
              @endif
            </td>
            <td></td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
