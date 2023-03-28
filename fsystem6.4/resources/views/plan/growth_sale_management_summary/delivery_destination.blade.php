@extends('layout')

@section('title')
{{ __('view.plan.growth_sale_management_summary.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_sale_management_summary.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form  class="form-horizontal basic-form" action="{{ route('plan.growth_sale_management_summary.search') }}" method="POST">
    <div class="col-md-10 col-sm-12 col-xs-12 col-md-offset-1">
      <search-growth-management-summary-form
        :factories="{{ $factories }}"
        :species-list="{{ $species_list }}"
        :search-params="{{ json_encode($params) }}"
        :belongs-to-factory="{{ json_encode(Auth::user()->affiliation->belongsToFactory()) }}">
      </search-growth-management-summary-form>
    </div>
    <div class="col-md-10 col-sm-12 col-xs-12 col-md-offset-1">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

<div class="row">
  <div class="col-md-offset-1">
    <p>
      <b>
        {{ $species->species_name }}&nbsp;-&nbsp;
        {{ $delivery_destination->delivery_destination_abbreviation }}&nbsp;-&nbsp;
        {{ $params['display_unit'] === 'weight' ? __('view.global.kilogram') : __('view.global.quantity') }}
      </b>
    </p>
  </div>
  <div class="col-md-offset-1 pull-left growth-sale-management-summary delivery-destinations">
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          <th class="date-header">&nbsp;</th>
        </tr>
        @foreach ($factories_with_order as $f)
          <tr>
            <th class="text-left">{{ $f->factory_abbreviation }}</th>
          </tr>
          @foreach ($f->factory_products as $fp)
            <tr>
              <th class="text-left">&nbsp;&nbsp;{{ $fp->factory_product_abbreviation }}</th>
            </tr>
          @endforeach
        @endforeach
        <tr>
          <th class="text-center">{{ __('view.plan.growth_sale_management_summary.shipping_total') }}</th>
        </tr>
      </thead>
    </table>
  </div>

  <div class="summary-data">
    @if ($params['display_term'] === 'date')
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <th class="date-header summary-data">
              @php ($shipping_date = $hd->getDefaultShippingDate())
              {{ $shipping_date->format('n/j') }}<br>({{ $shipping_date->dayOfWeekJa() }})
            </th>
            @endforeach
            <th class="date-header summary-data">{{ __('view.plan.growth_sale_management_summary.week_total') }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($factories_with_order as $factory)
          <tr>
            @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
              @foreach ($harvesting_dates as $hd)
              <th class="text-right">
                {{ number_format_of_product($factory->factory_products->toSumPerDate($week, $hd->format('Ymd'), $params['display_unit']), $params['display_unit']) }}
              </th>
              @endforeach
              <th class="text-right">
                {{ number_format_of_product($factory->factory_products->toSumPerWeek($week, $params['display_unit']), $params['display_unit']) }}
              </th>
            @endforeach
          </tr>
          @foreach ($factory->factory_products as $fp)
          <tr>
            @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
              @foreach ($harvesting_dates as $hd)
              <td class="text-right">
                @if ($params['display_unit'] === 'weight')
                {{ number_format_of_product($fp->orders[$week]['weights'][$hd->format('Ymd')], $params['display_unit']) }}
                @endif
                @if ($params['display_unit'] === 'quantity')
                {{ number_format_of_product($fp->orders[$week]['quantities'][$hd->format('Ymd')], $params['display_unit']) }}
                @endif
              </td>
              @endforeach
              <td class="text-right">
                @if ($params['display_unit'] === 'weight')
                {{ number_format_of_product($fp->orders[$week]['weights']['total'], $params['display_unit']) }}
                @endif
                @if ($params['display_unit'] === 'quantity')
                {{ number_format_of_product($fp->orders[$week]['quantities']['total'], $params['display_unit']) }}
                @endif
              </td>
            @endforeach
          </tr>
          @endforeach
        @endforeach
        <tr>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <th class="text-right">
              {{ number_format_of_product($factories_with_order->toSumOfWholePerDate($week, $hd->format('Ymd'), $params['display_unit']), $params['display_unit']) }}
            </th>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($factories_with_order->toSumOfWholePerWeek($week, $params['display_unit']), $params['display_unit']) }}
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
        @foreach ($factories_with_order as $factory)
          <tr>
            @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            <th class="text-right">
              {{ number_format_of_product($factory->factory_products->toSumPerMonth($harvesting_month->format('Ym'), $params['display_unit']), $params['display_unit']) }}
            </th>
            @endforeach
          </tr>
          @foreach ($factory->factory_products as $fp)
          <tr>
            @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            <td class="text-right">
              @if ($params['display_unit'] === 'weight')
              {{ number_format_of_product($fp->orders['weights'][$harvesting_month->format('Ym')], $params['display_unit']) }}
              @endif
              @if ($params['display_unit'] === 'quantity')
              {{ number_format_of_product($fp->orders['quantities'][$harvesting_month->format('Ym')], $params['display_unit']) }}
              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
        @endforeach
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <th class="text-right summary-data">
            {{ number_format_of_product($factories_with_order->toSumOfWholePerMonth($harvesting_month->format('Ym'), $params['display_unit']), $params['display_unit']) }}
          </th>
          @endforeach
        </tr>
      </tbody>
    </table>
    @endif
  </div>
</div>
@endsection
