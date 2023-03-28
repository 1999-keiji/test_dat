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

@section('styles')
  @parent

  <style>
    tr.factory-product-row {
      border-top: 3.5px double #4f6228;
      height: 2.2em;
    }
    tr.factory-product-row>th.factory-product-name {
      font-size: 1.15em;
    }
    td.delivery-destination-row {
      border-bottom: 1.5px solid #4f6228;
      border-right: 1.5px solid #4f6228;
      font-weight: bold;
    }
    tr.divider {
      height: 3px;
    }
    tr.divider>td.buffer {
      border-left: none;
      border-right: none;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('plan.growth_sale_management_summary.search') }}" method="POST">
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
        {{ $factory->factory_abbreviation }}&nbsp;-&nbsp;
        {{ $species->species_name }}&nbsp;-&nbsp;
        {{ $params['display_unit'] === 'weight' ? __('view.global.kilogram') : __('view.global.quantity') }}
      </b>
    </p>
  </div>
  <div class="col-md-offset-1 pull-left growth-sale-management-summary factory-species">
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          <th class="date-header" colspan="2">&nbsp;</th>
        </tr>
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.harvesting_quantity') }}</th>
        </tr>
        @if ($params['display_term'] === 'date')
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.product_rate') }}&nbsp;（%）</th>
        </tr>
        @endif
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.product_weight') }}</th>
          </tr>
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.order') }}</th>
        </tr>
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.gap') }}</th>
        </tr>
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.discard') }}</th>
        </tr>
        <tr>
          <th class="text-left" colspan="2">{{ __('view.plan.growth_sale_management.stock') }}</th>
        </tr>
        <tr class="divider">
          <td class="buffer" colspan="2"></td>
        </tr>
        <tr>
          <th class="date-header">{{ __('view.master.products.product') }}&nbsp;/&nbsp;{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
          <th class="date-header">{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
        </tr>
        @foreach ($factory_products_with_order as $ps)
          <tr class="factory-product-row">
            <th class="text-left factory-product-name" colspan="2">
              {{ $ps->number_of_heads }}{{ __('view.global.stock') }}
              {{ $ps->weight_per_number_of_heads }}g
              {{ $input_group_list[$ps->input_group] }}
            </th>
          </tr>
          @foreach ($ps->factory_products as $ps)
            @foreach ($ps->delivery_destinations as $dd)
            <tr>
              <td class="delivery-destination-row">{{ $dd->delivery_destination_abbreviation }}</td>
              <td class="delivery-destination-row">{{ $dd->delivery_lead_time->withSuffix() }}</td>
            </tr>
            @endforeach
          @endforeach
        @endforeach
        <tr class="factory-product-row">
          <th colspan="2">{{ __('view.plan.growth_sale_management_summary.shipping_total') }}</th>
        </tr>
      </thead>
    </table>
  </div>

  <div class="summary-data factory-species">
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
            <td class="text-right">{{ number_format($summary['product_rates'][$week][$hd->format('Ymd')], 2) }}</td>
            @endforeach
            <th>&nbsp;</th>
          @endforeach
        </tr>
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">
              {{ number_format_of_product($summary['product_weights'][$week][$hd->format('Ymd')]) }}
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($summary['product_weights'][$week]['total']) }}
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
              {{ number_format_of_product($summary['order_weights'][$week][$hd->format('Ymd')]) }}
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($summary['order_weights'][$week]['total']) }}
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
                {{ number_format_of_product($gap) }}
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
                {{ number_format_of_product($summary['disposal_weights'][$week][$hd->format('Ymd')]) }}
              </td>
            @endforeach
            <th class="text-right">
                {{ number_format_of_product($summary['disposal_weights'][$week]['total']) }}
            </th>
          @endforeach
        </tr>
        <tr>
          <td class="text-right">
            {{ number_format_of_product($summary['prev_carry_over_stock_weight']) }}
          </td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($stock = $summary['stocks'][$week][$hd->format('Ymd')])
              <td class="text-right
                @if ($stock < 0)
                text-danger
                @endif">
                {{ number_format_of_product($stock) }}
              </td>
            @endforeach
            <th class="text-right
              @if ($stock < 0)
              text-danger
              @endif">
              {{ number_format_of_product($stock) }}
            </th>
          @endforeach
        </tr>
        <tr class="divider">
          <td class="buffer"></td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="buffer"></td>
            @endforeach
            <td class="buffer"></td>
          @endforeach
        </tr>
        <tr>
          <th class="date-header">
            {{ __('view.plan.growth_sale_management_summary.forwarded') }}<br>
            {{ __('view.plan.growth_sale_management.stock') }}
          </th>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <th class="date-header">
              @php ($shipplig_date = $hd->getDefaultShippingDate())
              {{ $shipplig_date->format('n/j') }}<br>({{ $shipplig_date->dayOfWeekJa() }})
            </th>
            @endforeach
            <th class="date-header summary-data">{{ __('view.plan.growth_sale_management_summary.week_total') }}</th>
          @endforeach
        </tr>
        @foreach ($factory_products_with_order as $ps)
          <tr class="factory-product-row">
            <th class="text-right">{{ number_format_of_product($ps->weight_of_carry_over_stock) }}</th>
            @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
              @foreach ($harvesting_dates as $hd)
              <th class="text-right">
                {{ number_format_of_product(
                  $ps->factory_products->map(function ($fp) use ($week, $hd, $params) {
                    return $fp->delivery_destinations->toSumPerDate($week, $hd->format('Ymd'), $params['display_unit']);
                  })
                  ->sum(),
                  $params['display_unit']
                ) }}
              </th>
              @endforeach
              <th class="text-right">
                {{ number_format_of_product(
                  $ps->factory_products->map(function ($fp) use ($week, $params) {
                    return $fp->delivery_destinations->toSumPerWeek($week, $params['display_unit']);
                  })
                  ->sum(),
                  $params['display_unit']
                ) }}
              </th>
            @endforeach
          </tr>
          @foreach ($ps->factory_products as $fp)
            @foreach ($fp->delivery_destinations as $dd)
            <tr>
              <td></td>
              @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
                @foreach ($harvesting_dates as $hd)
                  <td class="text-right
                    @if ($dd->orders[$week]['not_forecasted_order'][$hd->format('Ymd')])
                    not-forecasted-order
                    @endif
                    @if ($dd->orders[$week]['not_forecasted_order'][$hd->format('Ymd')] && $dd->orders[$week]['only_fixed_order'][$hd->format('Ymd')])
                    only-fixed-order
                    @endif
                    @if ($dd->shipment_lead_time->willShipOnTheDate() && $dd->orders[$week]['quantities'][$hd->format('Ymd')] !== 0)
                    text-danger
                    @endif">
                    @if ($params['display_unit'] === 'weight')
                    {{ number_format_of_product($dd->orders[$week]['weights'][$hd->format('Ymd')], $params['display_unit']) }}
                    @endif
                    @if ($params['display_unit'] === 'quantity')
                    {{ number_format_of_product($dd->orders[$week]['quantities'][$hd->format('Ymd')], $params['display_unit']) }}
                    @endif
                  </td>
                @endforeach
                <th class="text-right">
                  @if ($params['display_unit'] === 'weight')
                  {{ number_format_of_product($dd->orders[$week]['weights']['total'], $params['display_unit']) }}
                  @endif
                  @if ($params['display_unit'] === 'quantity')
                  {{ number_format_of_product($dd->orders[$week]['quantities']['total'], $params['display_unit']) }}
                  @endif
                </th>
              @endforeach
            </tr>
            @endforeach
          @endforeach
        @endforeach
        <tr class="factory-product-row">
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
              {{ number_format_of_product($summary['order_weights'][$week][$hd->format('Ymd')]) }}
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($summary['order_weights'][$week]['total']) }}
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
            {{ number_format_of_product($summary['product_weights'][$harvesting_month->format('Ym')]) }}
          </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($summary['order_weights'][$harvesting_month->format('Ym')]) }}
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
              {{ number_format_of_product($gap) }}
            </td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            <td class="text-right">
              {{ number_format_of_product($summary['disposal_weights'][$harvesting_month->format('Ym')]) }}
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
              {{ number_format_of_product($stock) }}
            </td>
          @endforeach
        </tr>
        <tr class="divider">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="buffer"></td>
          @endforeach
        </tr>
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <th class="date-header">{{ $harvesting_month->format('n') }}月</th>
          @endforeach
        </tr>
        @foreach ($factory_products_with_order as $ps)
          <tr class="factory-product-row">
            @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            <th class="text-right">
              {{ number_format_of_product(
                $ps->factory_products->map(function ($fp) use ($params, $harvesting_month) {
                  return $fp->delivery_destinations->toSumPerMonth($harvesting_month->format('Ym'), $params['display_unit']);
                })
                ->sum(),
                $params['display_unit']
              ) }}
            </th>
            @endforeach
          </tr>
          @foreach ($ps->factory_products as $fp)
            @foreach ($fp->delivery_destinations as $dd)
            <tr>
              @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
              <td class="text-right">
                @if ($params['display_unit'] === 'weight')
                {{ number_format_of_product($dd->orders['weights'][$harvesting_month->format('Ym')], $params['display_unit']) }}
                @endif
                @if ($params['display_unit'] === 'quantity')
                {{ number_format_of_product($dd->orders['quantities'][$harvesting_month->format('Ym')], $params['display_unit']) }}
                @endif
              </td>
              @endforeach
            </tr>
            @endforeach
          @endforeach
        @endforeach
        <tr class="factory-product-row">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($summary['order_weights'][$harvesting_month->format('Ym')]) }}
          </td>
          @endforeach
        </tr>
      </tbody>
    </table>
    @endif
  </div>
</div>
@endsection
