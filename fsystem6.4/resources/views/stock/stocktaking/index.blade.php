@extends('layout')

@section('title')
{{ __('view.stock.stocktaking.index') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
<li>{{ __('view.stock.stocktaking.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('stock.stocktaking.search') }}" method="POST">
    <search-stocktaking-form
      :factories="{{ $factories }}"
      :search-params="{{ json_encode($params ?: new \stdClass()) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
      :errors="{{ $errors }}">
    </search-stocktaking-form>
    <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-2 col-sm-offset-1">
      <button class="btn btn-lg btn-default pull-left" type="submit">
        <i class="fa fa-search"></i>&nbsp;{{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p><span class="search-result-count">{{ count($stocktaking_details)}}</span>{{ __('view.global.suffix_serach_result_count') }}</p>
  </div>
</div>

<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.weight') }}{{ __('view.global.total') }}(g)</th>
          <th>{{ __('view.global.stock_quantity') }}{{ __('view.global.total') }}</th>
          <th>{{ __('view.stock.stocktaking.stocktaking') }}{{ __('view.global.completed_at') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-right">{{ number_format($stocktaking_details->totalOfWeight()) }}</td>
          <td class="text-right">{{ number_format($stocktaking_details->totalOfStockQuantity()) }}</td>
          <td>{{ $stocktaking->stocktaking_comp_at ?? '' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-md-8 col-sm-8 col-xs-8">
    <div class="row">
      @if ($stocktaking->hasNotStartedYet())
      <form action="{{ route('stock.stocktaking.start') }}" method="POST">
        <a class="btn btn-default" href="{{ route('stock.stocktaking.export') }}">
          {{ __('view.stock.stocktaking.stocktaking_file') }}{{ __('view.global.download') }}
        </a>
        <button class="btn btn-default" type="submit">{{ __('view.stock.stocktaking.start_stocktaking') }}</button>
        {{ csrf_field() }}
        {{ method_field('POST') }}
      </form>
      @endif
      @if ($stocktaking->hasBeenStocktakingNow())
      <form action="#" method="POST">
        <a class="btn btn-default" href="{{ route('stock.stocktaking.export') }}">
          {{ __('view.stock.stocktaking.stocktaking_file') }}{{ __('view.global.download') }}
        </a>
        <button class="btn btn-default save-stocktaking" value="{{ route('stock.stocktaking.keep', $stocktaking->getJoinedPrimaryKeys()) }}" type="button">
          {{ __('view.stock.stocktaking.keep_stocktaking') }}
        </button>
        <button id="refresh-stocktaking" class="btn btn-default" value="{{ route('stock.stocktaking.refresh', $stocktaking->getJoinedPrimaryKeys()) }}" type="button">
          {{ __('view.stock.stocktaking.refresh_stocktaking') }}
        </button>
        <button class="btn btn-default save-stocktaking" value="{{ route('stock.stocktaking.complete', $stocktaking->getJoinedPrimaryKeys()) }}" type="button">
          {{ __('view.stock.stocktaking.complete_stocktaking') }}
        </button>
        {{ csrf_field() }}
        {{ method_field('') }}
      </form>
      @endif
      @if ($stocktaking->hasBeenStoppedStocktaking())
      <form action="#" method="POST">
        <a class="btn btn-default" href="{{ route('stock.stocktaking.export') }}">
          {{ __('view.stock.stocktaking.stocktaking_file') }}{{ __('view.global.download') }}
        </a>
        <button id="restart-stocktaking" class="btn btn-default" value="{{ route('stock.stocktaking.restart', $stocktaking->getJoinedPrimaryKeys()) }}" type="button">
          {{ __('view.stock.stocktaking.restart_stocktaking') }}
        </button>
        <button id="refresh-stocktaking" class="btn btn-default" value="{{ route('stock.stocktaking.refresh', $stocktaking->getJoinedPrimaryKeys()) }}" type="button">
          {{ __('view.stock.stocktaking.refresh_stocktaking') }}
        </button>
        {{ csrf_field() }}
        {{ method_field('') }}
      </form>
      @endif
      @if ($stocktaking->hasCompleted())
      <form action="#" method="POST">
        <a class="btn btn-default" href="{{ route('stock.stocktaking.export') }}">
          {{ __('view.stock.stocktaking.stocktaking_file') }}{{ __('view.global.download') }}
        </a>
        <a class="btn btn-default" href="{{ route('stock.stocktaking.export.transition', $stocktaking->getJoinedPrimaryKeys()) }}">
          {{ __('view.stock.stocktaking.transition_file') }}{{ __('view.global.download') }}
        </a>
        {{ csrf_field() }}
        {{ method_field('') }}
      </form>
      @endif
    </div>
  </div>
</div>

<div class="row">
  <form id="save-stocktaking-form" action="#" method="POST">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.master.species.species') }}</th>
          <th>{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
          <th>@sortablelink('number_of_heads', __('view.master.factory_products.packaging_style'))</th>
          <th>{{ __('view.master.factory_products.number_of_heads') }}</th>
          <th>
            {{ __('view.stock.stocktaking.before_stocktaking') }}<br>
            {{ __('view.stock.stocks.stock_quantity') }}<br>
            ({{ __('view.global.pack_quantity') }})
          </th>
          <th>{{ __('view.stock.stocktaking.actual_stock_quantity') }}</th>
          <th>{{ __('view.stock.stocktaking.stock_difference') }}</th>
          <th>{{ __('view.global.weight') }}(kg)</th>
          <th>{{ __('view.global.stock_quantity') }}</th>
          <th>{{ __('view.global.remark') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($stocktaking_details as $idx => $sd)
        <tr>
          <td class="text-left">{{ $sd->species_name }}</td>
          <td class="text-left
            @if (! $sd->hasAllocated())
            text-danger
            @endif">{{ $sd->delivery_destination_abbreviation }}</td>
          <td class="text-left">
            {{ $sd->number_of_heads }}{{ __('view.global.stock') }}
            {{ $sd->weight_per_number_of_heads }}g
            {{ $input_group_list[$sd->input_group] }}
          </td>
          <td class="text-right">{{ $sd->number_of_cases ?: '' }}</td>
          <td class="text-right">
            {{ $sd->hasAllocated() ? number_format($sd->getStockQuantity()) : '('.number_format($sd->getStockQuantity()).')' }}
          </td>
          <td class="text-right">
            {{ $sd->hasAllocated() ? number_format($sd->getActualStockQuantity()) : '('.number_format($sd->getActualStockQuantity()).')' }}
            <input type="hidden" name="stocktaking_details[{{ $idx }}][actual_stock_quantity]" value="{{ $sd->actual_stock_quantity }}">
          </td>
          <td class="text-right">{{ number_format($sd->getStockDifference()) }}</td>
          <td class="text-right">{{ number_format(convert_to_kilogram($sd->getStocktakingWeight()), 2) }}</td>
          <td class="text-right">{{ number_format($sd->getSumOfStockQuantity()) }}</td>
          <td class="text-left">
            @if ($stocktaking->hasBeenStocktakingNow())
            <input class="form-control" type="text" maxlength="255" name="stocktaking_details[{{ $idx }}][remark]" value="{{ $sd->remark }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][species_code]" value="{{ $sd->species_code }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][number_of_heads]" value="{{ $sd->number_of_heads }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][weight_per_number_of_heads]" value="{{ $sd->weight_per_number_of_heads }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][input_group]" value="{{ $sd->input_group }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][number_of_cases]" value="{{ $sd->number_of_cases }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][delivery_destination_code]" value="{{ $sd->delivery_destination_code }}">
            <input type="hidden" name="stocktaking_details[{{ $idx }}][unit]" value="{{ $sd->unit }}">
            @else
            {{ $sd->remark }}
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ csrf_field() }}
    {{ method_field('PATCH') }}
  </form>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $(function () {
      $('#refresh-stocktaking').click(function () {
        if (! confirm('在庫棚卸処理をやり直しますか?')) {
          return
        }

        $(this).prop('disabled', true)

        var $form = $(this).parent('form')
        $form.attr('action', $(this).val())
        $form.find('input[name="_method"]').val('DELETE')
        $form.submit()
      })

      $('#restart-stocktaking').click(function () {
        $(this).prop('disabled', true)

        var $form = $(this).parent('form')
        $form.attr('action', $(this).val())
        $form.find('input[name="_method"]').val('PATCH')
        $form.submit()
      })

      $('.save-stocktaking').click(function () {
        if (! confirm('在庫棚卸データを登録しますか?')) {
          return
        }

        $(this).prop('disabled', true)

        $('#save-stocktaking-form').attr('action', $(this).val())
        $('#save-stocktaking-form').submit()
      })
    })
  </script>
@endsection
