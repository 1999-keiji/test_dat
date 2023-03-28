@extends('layout')

@section('title')
{{ __('view.stock.stocks.index') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.stock.stocks.index') }}</li>
@endsection

@section('content')
@if ($params['refered_from_summary'] ?? false)
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('stock.stocks.summary.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>
@endif
<search-stocks-form
  search-stocks-action="{{ route('stock.stocks.search') }}"
  export-stocks-action="{{ route('stock.stocks.export') }}"
  :factories="{{ $factories }}"
  :stock-status-list="{{ json_encode($stock_status_list) }}"
  :input-group-list="{{ json_encode($input_group_list) }}"
  :allocation-status-list="{{ json_encode($allocation_status_list) }}"
  :disposal-status-list="{{ json_encode($disposal_status_list_except_disposal) }}"
  :search-params="{{ json_encode($params ?: new \stdClass()) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}">
</search-stocks-form>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p><span class="search-result-count">{{ $stocks->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}</p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1">
    {{ $stocks->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12 horizontal-scroll-table">
    <table class="table table-color-bordered table-more-condensed sticky-table">
      <tbody>
        <tr class="header">
          @canSave (Auth::user(), route_relatively('stock.stocks.index'))
          <th>{{ __('view.stock.stocks.move') }}</th>
          <th>{{ __('view.stock.stocks.adjust') }}</th>
          @endcanSave
          @canAccess (Auth::user(), route_relatively('stock.stocks.dispose.index'))
          <th>{{ __('view.stock.stocks.dispose') }}</th>
          @endcanAccess
          <th>@sortablelink('stocks.warehouse_code', __('view.master.warehouses.warehouse_storage'))</th>
          <th>{{ __('view.global.status') }}</th>
          <th>@sortablelink('stocks.species_code', __('view.master.species.species'))</th>
          <th>{{ __('view.master.factory_products.packaging_style') }}</th>
          <th>{{ __('view.stock.stocks.stock_quantity') }}</th>
          <th>{{ __('view.global.weight') }}(kg)</th>
          <th>@sortablelink('stocks.harvesting_date', __('view.shipment.global.harvesting_date'))</th>
          <th>{{ __('view.stock.stocks.expired_on') }}</th>
          <th>@sortablelink('sub_query.delivery_destination_code', __('view.master.delivery_destinations.delivery_destination'))</th>
          <th>@sortablelink('sub_query.delivery_date', __('view.order.order_list.delivery_date'))</th>
          <th>{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
          <th>{{ __('view.stock.stocks.disposal_quantity') }}</th>
          <th>{{ __('view.stock.stocks.disposal_weight') }}(kg)</th>
        </tr>
        @foreach ($stocks as $s)
        <tr
          @if ($s->isExpired())
          class="danger"
          @endif>
          @if (! $s->hasAllocated())
          @canSave (Auth::user(), route_relatively('stock.stocks.index'))
          <td>
            <a class="btn btn-sm btn-info right-margined-btn" href="{{ route('stock.stocks.move', $s->stock_id) }}">
              {{ __('view.stock.stocks.move') }}
            </a>
          </td>
          <td>
            <a class="btn btn-sm btn-warning right-margined-btn" href="{{ route('stock.stocks.adjust', $s->stock_id) }}">
              {{ __('view.stock.stocks.adjust') }}
            </a>
          </td>
          @endcanSave
          @canAccess (Auth::user(), route_relatively('stock.stocks.dispose.index'))
          <td>
            <form action="{{ route('stock.stocks.dispose.search') }}" method="POST">
              <button class="btn btn-sm btn-danger right-margined-btn" type="submit">
                {{ __('view.stock.stocks.dispose') }}
              </button>
              <input name="factory_code" type="hidden" value="{{ $s->factory_code }}">
              <input name="warehouse_code" type="hidden" value="{{ $s->warehouse_code }}">
              <input name="harvesting_month" type="hidden" value="{{ $s->getHarvestingDate()->format('Y/m') }}">
              <input name="stock_status" type="hidden" value="{{ $s->stock_status }}">
              <input name="species_code" type="hidden" value="{{ $s->species_code }}">
              <input name="number_of_heads" type="hidden" value="{{ $s->number_of_heads }}">
              <input name="weight_per_number_of_heads" type="hidden" value="{{ $s->weight_per_number_of_heads }}">
              <input name="input_group" type="hidden" value="{{ $s->input_group }}">
              <input name="disposal_status" type="hidden" value="">
              <input name="refered_from_index" type="hidden" value="1">
              {{ csrf_field() }}
              {{ method_field('POST') }}
            </form>
          </td>
          @endcanAccess
          @else
          <td></td>
          <td></td>
          <td></td>
          @endif
          <td class="text-left">{{ $s->storage_warehouse }}</td>
          <td class="text-left">{{ array_flip($stock_status_list)[$s->stock_status] }}</td>
          <td class="text-left">{{ $s->species_name}}</td>
          <td class="text-left">
            {{ $s->number_of_heads }}{{ __('view.global.stock') }}
            {{ $s->weight_per_number_of_heads }}g
            {{ $input_group_list[$s->input_group] }}
          </td>
          <td class="text-right">{{ number_format($s->stock_quantity) }}</td>
          <td class="text-right">{{ number_format_of_product(convert_to_kilogram($s->stock_weight), 'weight') }}</td>
          <td>{{ $s->getHarvestingDate()->formatWithDayOfWeek() }}</td>
          <td>{{ $s->getExpiredOn()->formatWithDayOfWeek() }}</td>
          <td class="text-left">{{ $s->delivery_destination_abbreviation ?: '未引当' }}</td>
          <td>{{ $s->delivery_date }}</td>
          <td>
            @if ($s->delivery_lead_time !== null)
            {{ $s->delivery_lead_time }}{{ __('view.global.day') }}
            @endif
          </td>
          <td class="text-right">{{ number_format($s->disposal_quantity) }}</td>
          <td class="text-right">{{ number_format_of_product(convert_to_kilogram($s->disposal_weight), 'weight') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
