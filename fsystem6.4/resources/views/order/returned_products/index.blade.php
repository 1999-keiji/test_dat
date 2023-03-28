@extends('layout')

@section('title')
{{ __('view.order.return_input.index') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.order.return_input.index') }}</li>
@endsection

@section('styles')
  @parent

  <style>
    table.returned-products-table>thead>tr>th {
      min-width: 10rem;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('order.returned_products.search') }}" method="POST">
    <search-returned-products-form
      :factories="{{ $factories }}"
      :search-params="{{ json_encode($params ?: new \stdClass()) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}">
    </search-returned-products-form>
    <div class="col-md-2 col-sm-2 col-xs-2">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params))
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $orders->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-9 col-md-offset-2 col-sm-offset-2">
    {{ $orders->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-10 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1 horizontal-scroll-table">
    <table class="table table-color-bordered table-more-condensed returned-products-table">
      <thead>
        <tr>
          @canSave(Auth::user(), route_relatively('order.returned_products.index'))
          <th>{{ __('view.order.return_input.return') }}</th>
          @endcanSave
          <th>@sortablelink('order_number', __('view.order.order_list.order_number'))</th>
          <th>@sortablelink('received_date', __('view.order.order_list.received_date'))</th>
          <th>@sortablelink('delivery_date', __('view.order.order_list.delivery_date'))</th>
          <th>@sortablelink('end_user_code', __('view.master.end_users.end_user'))</th>
          <th>@sortablelink('delivery_destination_code', __('view.master.delivery_destinations.delivery_destination'))</th>
          <th>@sortablelink('product_name', __('view.master.products.product'))</th>
          <th>{{ __('view.order.order_list.order_unit') }}</th>
          <th>{{ __('view.master.global.currency_code') }}</th>
          <th>{{ __('view.order.order_list.order_quantity') }}</th>
          <th>{{ __('view.order.return_input.returned_on') }}</th>
          <th>{{ __('view.order.return_input.return_product') }}</th>
          <th>{{ __('view.order.return_input.return_unit_price') }}</th>
          <th>{{ __('view.order.return_input.returned_product_quantity') }}</th>
          <th>{{ __('view.order.return_input.returned_amount') }}</th>
          <th>{{ __('view.order.order_list.order_amount') }}</th>
          <th>{{ __('view.order.order_list.base_plus_order_number') }}</th>
          <th>{{ __('view.order.order_list.end_user_order_number') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $o)
        <tr>
          @canSave(Auth::user(), route_relatively('order.returned_products.index'))
          <td>
            <return-input-form
              route-action="{{ route('order.returned_products.create', $o->order_number) }}"
              :order="{{ $o }}"
              :old-params="{{ json_encode(old('order_number') === $o->order_number ? (old() ?: new \stdClass()) : new \stdClass()) }}">
            </return-input-form>
          </td>
          @endcanSave
          <td>{{ $o->order_number }}</td>
          <td>{{ $o->received_date }}</td>
          <td>{{ $o->delivery_date }}</td>
          <td class="text-left">{{ $o->end_user_abbreviation }}</td>
          <td class="text-left">{{ $o->delivery_destination_abbreviation }}</td>
          <td class="text-left">{{ $o->product_name }}</td>
          <td class="text-right">{{ $o->formatOrderUnit() }}</td>
          <td>{{ $o->currency_code }}</td>
          <td class="text-right">{{ number_format($o->order_quantity) }}&nbsp;{{ $o->unit }}</td>
          @if ($o->returned_order_number)
          <td>{{ $o->returned_on }}</td>
          <td class="text-left">{{ $o->returned_product_name }}</td>
          <td class="text-right">{{ $o->formatReturnedUnitPrice() }}</td>
          <td class="text-right">{{ number_format($o->returned_quantity) }}&nbsp;{{ $o->unit }}</td>
          <td class="text-right">{{ $o->formatReturnedAmount() }}</td>
          @endif
          @if (! $o->returned_order_number)
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          @endif
          <td class="text-right">{{ $o->formatAmountExceptReturned() }}</td>
          <td class="text-left">{{ $o->getBasePlusOrderNumber() }}</td>
          <td class="text-left">{{ $o->end_user_order_number }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
