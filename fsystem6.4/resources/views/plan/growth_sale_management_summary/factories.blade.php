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
      <b>{{ __('view.plan.growth_sale_management.all_factory') }}&nbsp;-&nbsp;{{ $species->species_name }}&nbsp;-&nbsp;{{ __('view.global.kilogram') }}</b>
    </p>
  </div>
  <div class="col-md-offset-1 pull-left growth-sale-management-summary factories">
    <table class="table table-color-bordered table-more-condensed growth-sale-management-summary-table">
      <thead>
        <tr>
          <th class="date-header">{{ __('view.master.factories.factory') }}</th>
        </tr>
        <tr>
          <th class="text-left">
            {{ __('view.plan.growth_sale_management.product_weight') }}
            <a data-toggle="collapse" href=".product_weights">▼</a>
          </th>
        </tr>
        @foreach ($factories_with_summary as $f)
        <tr class="product_weights collapse">
          <th class="text-left">
            &nbsp;&nbsp;
            <a class="anchor-to-summary-per-factory-species" href="#" data-factory-code="{{ $f->factory_code }}">
              {{ $f->factory_abbreviation }}
            </a>
          </th>
        </tr>
        @endforeach
        <tr>
          <th class="text-left">
            {{ __('view.plan.growth_sale_management.order') }}
            <a data-toggle="collapse" href=".order_weights">▼</a>
          </th>
        </tr>
        @foreach ($factories_with_summary as $f)
        <tr class="order_weights collapse">
          <th class="text-left">
            &nbsp;&nbsp;
            <a class="anchor-to-summary-per-factory-species" href="#" data-factory-code="{{ $f->factory_code }}">
              {{ $f->factory_abbreviation }}
            </a>
          </th>
        </tr>
        @endforeach
        <tr>
          <th class="text-left">
            {{ __('view.plan.growth_sale_management.gap') }}
            <a data-toggle="collapse" href=".gaps">▼</a>
          </th>
        </tr>
        @foreach ($factories_with_summary as $f)
        <tr class="gaps collapse">
          <th class="text-left">
            &nbsp;&nbsp;
            <a class="anchor-to-summary-per-factory-species" href="#" data-factory-code="{{ $f->factory_code }}">
              {{ $f->factory_abbreviation }}
            </a>
          </th>
        </tr>
        @endforeach
        <tr>
          <th class="text-left">
            {{ __('view.plan.growth_sale_management.discard') }}
            <a data-toggle="collapse" href=".discard">▼</a>
          </th>
        </tr>
        @foreach ($factories_with_summary as $f)
        <tr class="discard collapse">
          <th class="text-left">
            &nbsp;&nbsp;
            <a class="anchor-to-summary-per-factory-species" href="#" data-factory-code="{{ $f->factory_code }}">
              {{ $f->factory_abbreviation }}
            </a>
          </th>
        </tr>
        @endforeach
        <tr>
          <th class="text-left">
            {{ __('view.plan.growth_sale_management.stock') }}
            <a data-toggle="collapse" href=".stocks">▼</a>
          </th>
        </tr>
        @foreach ($factories_with_summary as $f)
        <tr class="stocks collapse">
          <th class="text-left">
            &nbsp;&nbsp;
            <a class="anchor-to-summary-per-factory-species" href="#" data-factory-code="{{ $f->factory_code }}">
              {{ $f->factory_abbreviation }}
            </a>
          </th>
        </tr>
        @endforeach
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
            <th class="text-right">
              {{ number_format_of_product($factories_with_summary->toSumPerDate($week, $hd->format('Ymd'), 'product_weights')) }}
            </th>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($factories_with_summary->toSumPerWeek($week, 'product_weights')) }}
            </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="product_weights collapse">
          <td></td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">
              {{ number_format_of_product($factory->summary['product_weights'][$week][$hd->format('Ymd')]) }}
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($factory->summary['product_weights'][$week]['total']) }}
            </th>
          @endforeach
        </tr>
        @endforeach
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <th class="text-right">
              {{ number_format_of_product($factories_with_summary->toSumPerDate($week, $hd->format('Ymd'), 'order_weights')) }}
            </th>
            @endforeach
            <th class="text-right">
                {{ number_format_of_product($factories_with_summary->toSumPerWeek($week, 'order_weights')) }}
            </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="order_weights collapse">
          <td></td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
            <td class="text-right">
              {{ number_format_of_product($factory->summary['order_weights'][$week][$hd->format('Ymd')]) }}
            </td>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($factory->summary['order_weights'][$week]['total']) }}
            </th>
          @endforeach
        </tr>
        @endforeach
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($gap = $factories_with_summary->toSumPerDate($week, $hd->format('Ymd'), 'gaps'))
              <th class="text-right
                @if ($gap < 0)
                text-danger
                @endif">
                {{ number_format_of_product($gap) }}
              </th>
            @endforeach
            <th></th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="gaps collapse">
          <td></td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($gap = $factory->summary['gaps'][$week][$hd->format('Ymd')])
              <td class="text-right
                @if ($gap < 0)
                text-danger
                @endif">
                {{ number_format_of_product($gap) }}
              </td>
            @endforeach
            <th></th>
          @endforeach
        </tr>
        @endforeach
        <tr>
          <td>&nbsp;</td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              <th class="text-right">
                {{ number_format_of_product($factories_with_summary->toSumPerDate($week, $hd->format('Ymd'), 'disposal_weights')) }}
              </th>
            @endforeach
            <th class="text-right">
              {{ number_format_of_product($factories_with_summary->toSumPerWeek($week, 'disposal_weights')) }}
            </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
          <tr class="discard collapse">
            <td></td>
            @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
              @foreach ($harvesting_dates as $hd)
                <td class="text-right">
                  {{ number_format_of_product($factory->summary['disposal_weights'][$week][$hd->format('Ymd')]) }}
                </td>
              @endforeach
              <th class="text-right">
                {{ number_format_of_product($factory->summary['disposal_weights'][$week]['total']) }}
              </th>
            @endforeach
          </tr>
        @endforeach
        <tr>
          <td></td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($stock = $factories_with_summary->toSumPerDate($week, $hd->format('Ymd'), 'stocks'))
              <th class="text-right
                @if ($stock < 0)
                text-danger
                @endif">
                {{ number_format_of_product($stock) }}
              </th>
            @endforeach
            <th class="text-right
              @if ($stock < 0)
              text-danger
              @endif">
              {{ number_format_of_product($stock) }}
            </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="stocks collapse">
          <td class="text-right">
            {{ number_format_of_product($factory->summary['prev_carry_over_stock_weight'])}}
          </td>
          @foreach ($harvesting_date->toListOfDatePerWeek($params['week_term']) as $week => $harvesting_dates)
            @foreach ($harvesting_dates as $hd)
              @php ($stock = $factory->summary['stocks'][$week][$hd->format('Ymd')])
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
        @endforeach
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
          <th class="text-right">
            {{ number_format_of_product($factories_with_summary->toSumPerMonth($harvesting_month->format('Ym'), 'product_weights')) }}
          </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="product_weights collapse">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($factory->summary['product_weights'][$harvesting_month->format('Ym')]) }}
          </td>
          @endforeach
        </tr>
        @endforeach
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <th class="text-right">
            {{ number_format_of_product($factories_with_summary->toSumPerMonth($harvesting_month->format('Ym'), 'order_weights')) }}
          </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="order_weights collapse">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($factory->summary['order_weights'][$harvesting_month->format('Ym')]) }}
          </td>
          @endforeach
        </tr>
        @endforeach
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            @php ($gap = $factories_with_summary->toSumPerMonth($harvesting_month->format('Ym'), 'gaps'))
            <th class="text-right
              @if ($gap < 0)
              text-danger
              @endif">{{ number_format_of_product($gap) }}</th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="gaps collapse">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            @php ($gap = $factory->summary['gaps'][$harvesting_month->format('Ym')])
            <td class="text-right
              @if ($gap < 0)
              text-danger
              @endif">
              {{ number_format_of_product($gap) }}
            </td>
          @endforeach
        </tr>
        @endforeach
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <th class="text-right">
            {{ number_format_of_product($factories_with_summary->toSumPerMonth($harvesting_month->format('Ym'), 'disposal_weights')) }}
          </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="discard collapse">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
          <td class="text-right">
            {{ number_format_of_product($factory->summary['disposal_weights'][$harvesting_month->format('Ym')]) }}
          </td>
          @endforeach
        </tr>
        @endforeach
        <tr>
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            @php ($stock = $factories_with_summary->toSumPerMonth($harvesting_month->format('Ym'), 'stocks'))
            <th class="text-right
              @if ($stock < 0)
              text-danger
              @endif">
              {{ number_format_of_product($stock) }}
            </th>
          @endforeach
        </tr>
        @foreach ($factories_with_summary as $factory)
        <tr class="stocks collapse">
          @foreach ($harvesting_date->toListOfMonth() as $harvesting_month)
            @php ($stock = $factory->summary['stocks'][$harvesting_month->format('Ym')])
            <td class="text-right
              @if ($stock < 0)
              text-danger
              @endif">
              {{ number_format_of_product($stock) }}
            </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    <form id="summary-per-factory-species-form" action="{{ route('plan.growth_sale_management_summary.search') }}" method="POST">
      <input type="hidden" name="display_type" value="factory_species">
      <input type="hidden" name="display_term" value="{{ $params['display_term'] }}">
      @if ($display_from_date = $params['display_from_date'] ?? '')
      <input type="hidden" name="display_from_date" value="{{ $display_from_date }}">
      @endif
      @if ($week_term = $params['week_term'] ?? '')
      <input type="hidden" name="week_term" value="{{ $week_term }}">
      @endif
      @if ($display_from_month = $params['display_from_month'] ?? '')
      <input type="hidden" name="display_from_month" value="{{ $display_from_month }}">
      @endif
      <input type="hidden" name="display_unit" value="{{ $params['display_unit']  }}">
      <input type="hidden" name="factory_code" value="">
      <input type="hidden" name="species_code" value="{{ $params['species_code']  }}">
      {{ csrf_field() }}
      {{ method_field('POST') }}
    </form>
  </div>
</div>
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('a.anchor-to-summary-per-factory-species').click(function () {
      var $form = $('#summary-per-factory-species-form')

      $form.find('input[name="factory_code"]').val($(this).data('factory-code'))
      $form.submit()
    })
  </script>
@endsection
