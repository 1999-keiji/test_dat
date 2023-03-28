@extends('layout')

@section('title')
{{ __('view.stock.stocks.summary') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.stock.stocks.summary') }}</li>
@endsection

@section('styles')
  @parent

  <style>
    table.sum-of-weight {
      width: 10%;
      height: 60px;
      margin-bottom: 3%;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('stock.stocks.summary.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-9 col-md-offset-1 col-sm-offset-1">
      <search-stock-summary-form
        :factories="{{ $factories }}"
        :allocation-status-list="{{ json_encode($allocation_status_list) }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :errors="{{ $errors }}">
      </search-stock-summary-form>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      <button class="btn btn-lg btn-default pull-right" type="submit">
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
    <p>
      <span class="search-result-count">{{ count($stock_summary_list)}}</span>
      {{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
</div>
<div class="row">
  <div class="col-md-9 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <table class="sum-of-weight table-color-bordered pull-right">
      <thead>
        <tr>
          <th class="text-center">
            {{ __('view.global.total') }}{{ __('view.global.weight') }}(Kg)
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-right">
            {{ number_format_of_product(convert_to_kilogram($stock_summary_list->sum('stock_weight')), 'weight') }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div class="col-md-9 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.list') }}</th>
          <th>@sortablelink('stocks.warehouse_code', __('view.master.warehouses.warehouse_storage'))</th>
          <th>@sortablelink('stocks.species_code', __('view.master.species.species'))</th>
          <th>@sortablelink('allocation_status', __('view.shipment.product_allocations.allocation_status'))</th>
          <th>{{ __('view.global.weight') }}(Kg)</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($stock_summary_list as $summary)
        <tr>
          <td>
            <form action="{{ route('stock.stocks.search') }}" method="POST">
              <button class="btn btn-sm btn-info" type="submit">{{ __('view.global.list') }}</button>
              <input type="hidden" name="factory_code" value="{{ $summary->factory_code }}">
              <input type="hidden" name="warehouse_code" value="{{ $summary->warehouse_code }}">
              <input type="hidden" name="species_code" value="{{ $summary->species_code }}">
              @if ($summary->allocation_status === '引当済')
              <input type="hidden" name="allocation_status" value="{{ $allocation_status_list['引当済'] }}">
              @else
              <input type="hidden" name="allocation_status" value="{{ $allocation_status_list['未引当'] }}">
              @endif
              <input type="hidden" name="disposal_status" value="{{ $disposal_status_list_except_disposal['在庫'] }}">
              <input type="hidden" name="refered_from_summary" value="1">
              {{ csrf_field() }}
              {{ method_field('POST') }}
            </form>
          </td>
          <td class="text-left">{{ $summary->storage_warehouse }}</td>
          <td class="text-left">{{ $summary->species_name }}</td>
          <td class="text-left">{{ $summary->allocation_status }}</td>
          <td class="text-right">{{ number_format_of_product(convert_to_kilogram($summary->stock_weight), 'weight') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
