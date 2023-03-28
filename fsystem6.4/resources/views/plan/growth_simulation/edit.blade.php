@extends('layout')

@section('title')
{{ __('view.plan.growth_simulation.add') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_simulation.add') }}</li>
@endsection

@section('styles')
  @parent

  <style type="text/css">
    input.datepicker-input {
      width: 100%;
      font-weight: normal;
    }
    input[name="display_from_date"],
    input[name="display_from_month"] {
      width: 120px;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-5">
    @if ($growth_simulation->hasFixed())
    <a class="btn btn-default btn-lg back-button can-transition" href="{{ route('plan.growth_simulation_fixed.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
    @else
    <a class="btn btn-default btn-lg back-button can-transition" href="{{ route('plan.growth_simulation.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
    @endif
  </div>
  <form action="{{ route('plan.growth_simulation.edit.search', $growth_simulation->getJoinedPrimaryKeys()) }}" method="POST">
    <div class="col-md-8 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <select-form-to-edit-simulation
        :factory="{{ $growth_simulation->factory_species->factory }}"
        :factory-species="{{ $growth_simulation->factory_species }}"
        :growth-simulation="{{ $growth_simulation }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        default-harvesting-date="{{ $default_harvesting_date }}"
        :display-kubun-list="{{ json_encode($display_kubun_list) }}">
      </select-form-to-edit-simulation>
    </div>
    <div class="col-md-1 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-2">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-offset-1 pull-left growth-sale-management-summary growth-simulation">
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          <th class="date-header">&nbsp;</th>
        </tr>
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.harvesting_quantity') }}</th></tr>
        @if ($params['display_term'] === 'date')
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.product_rate') }}</th></tr>
        @endif
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.product_weight') }}</th></tr>
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.order') }}</th></tr>
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.gap') }}</th></tr>
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.discard') }}</th></tr>
        <tr><th class="text-left">{{ __('view.plan.growth_sale_management.stock') }}</th></tr>
      </thead>
    </table>
  </div>

  <div class="summary-data">
    @if ($params['display_term'] === 'date')
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          <th class="date-header summary-data">
            {{ __('view.plan.growth_sale_management_summary.forwarded') }}<br>
            {{ __('view.plan.growth_sale_management.stock') }}
          </th>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <th class="date-header summary-data">{{ $hd->format('n/j') }}<br>({{ $hd->dayOfWeekJa() }})</th>
            @endforeach
            <th class="date-header summary-data">{{ __('view.plan.growth_sale_management_summary.week_total') }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">
              {{ number_format($summary['harvesting_quantities'][$week][$hd->format('Ymd')]) }}
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format($summary['harvesting_quantities'][$week]['total']) }}
            </th>
          @endforeach
        </tr>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">{{ number_format($summary['product_rates'][$week][$hd->format('Ymd')], 2) }}%</td>
            @endforeach
            <th>&nbsp;</th>
          @endforeach
        </tr>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">
              {{ number_format_of_product($summary['product_weights'][$week][$hd->format('Ymd')]) }}Kg
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($summary['product_weights'][$week]['total']) }}Kg
            </th>
          @endforeach
        </tr>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right
              @if ($summary['not_forecasted_order_flags'][$week][$hd->format('Ymd')])
              not-forecasted-order
              @endif
              @if ($summary['not_forecasted_order_flags'][$week][$hd->format('Ymd')] && $summary['only_fixed_order_flags'][$week][$hd->format('Ymd')])
              only-fixed-order
              @endif">
              {{ number_format_of_product($summary['order_weights'][$week][$hd->format('Ymd')]) }}Kg
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($summary['order_weights'][$week]['total']) }}Kg
            </th>
          @endforeach
        </tr>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($gap = $summary['gaps'][$week][$hd->format('Ymd')])
              <td class="text-right
                @if ($gap < 0)
                text-danger
                @endif">
                {{ number_format_of_product($gap) }}Kg
              </td>
            @endforeach
            <th>&nbsp;</th>
          @endforeach
        </tr>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">
              {{ number_format_of_product($summary['disposal_weights'][$week][$hd->format('Ymd')]) }}Kg
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($summary['disposal_weights'][$week]['total']) }}Kg
            </th>
          @endforeach
        </tr>
        <tr>
          <td class="text-right">
            {{ number_format_of_product($summary['prev_carry_over_stock_weight']) }}kg
          </td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($stock = $summary['stocks'][$week][$hd->format('Ymd')])
              <td class="text-right
                @if ($stock < 0)
                text-danger
                @endif">
                {{ number_format_of_product($stock) }}Kg
              </td>
            @endforeach
            <th class="text-right
              @if ($stock < 0)
              text-danger
              @endif">
              {{ number_format_of_product($stock) }}Kg
            </th>
          @endforeach
        </tr>
      </tbody>
    </table>
    @endif

    @if ($params['display_term'] === 'month')
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <th class="date-header summary-data">
            {{ $harvesting_month->format('n') }}
            {{ __('view.global.month')  }}
          </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format($summary['harvesting_quantities'][$harvesting_month->format('Ym')]) }}
          </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($summary['product_weights'][$harvesting_month->format('Ym')]) }}Kg
          </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($summary['order_weights'][$harvesting_month->format('Ym')]) }}Kg
          </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            @php ($gap = $summary['gaps'][$harvesting_month->format('Ym')])
            <td class="text-right
              @if ($gap < 0)
              text-danger
              @endif">
              {{ number_format_of_product($gap) }}Kg
            </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($summary['disposal_weights'][$harvesting_month->format('Ym')]) }}Kg
          </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            @php ($stock = $summary['stocks'][$harvesting_month->format('Ym')])
            <td class="text-right
              @if ($stock < 0)
              text-danger
              @endif">
              {{ number_format_of_product($stock) }}Kg
            </td>
          @endforeach
        </tr>
      </tbody>
    </table>
    @endif
  </div>
</div>

<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered growth-simulation-search">
      <tbody>
        <tr>
          <th>{{ __('view.plan.global.screen_change') }}</th>
          <td>
            <a href="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $growth_simulation->getFirstPortingDate()->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_cultivation_status_work.index') }}
            </a>
            <a href="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $growth_simulation->getFirstPortingDate()->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_cultivation_status_work.sum') }}
            </a>
            <a href="{{ route('plan.planned_arrangement_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $growth_simulation->getFirstPortingDate()->format('Y-m-d')]) }}">
              {{ __('view.plan.planned_arrangement_status_work.index') }}
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<update-simulation-form
  :growth-simulation="{{ $growth_simulation }}"
  :growth-simulation-items-list="{{ $growth_simulation->growth_simulation_items->groupByInputGroup() }}"
  :factory-growing-stages="{{ $factory_species->factory_growing_stages }}"
  :input-change-list="{{ json_encode($input_change_list) }}"
  :disabled-days-of-week-on-harvesting="{{ json_encode($simulation_date->disabledDaysOfWeekOnHarvesting($factory)) }}"
  :disabled-days-of-week-on-seeding="{{ json_encode($simulation_date->disabledDaysOfWeekOnSeeding($factory, $factory_species->factory_growing_stages)) }}"
  action-of-updating-simulation="{{ route('plan.growth_simulation.update', $growth_simulation->getJoinedPrimaryKeys()) }}"
  action-of-changing-simulation-name="{{ route('plan.growth_simulation.change-name', $growth_simulation->getJoinedPrimaryKeys()) }}"
  href-to-index-of-growth-simulations="{{ route('plan.growth_simulation.index') }}">
</update-simulation-form>
@endif
@endsection
