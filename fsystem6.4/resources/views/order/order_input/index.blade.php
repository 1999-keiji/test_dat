@extends('layout')

@section('title')
{{ __('view.order.order_input.index') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.order.order_input.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('order.order_input.search') }}" method="POST">
    <div class="col-md-9 col-sm-9 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <search-manual-created-orders-form
        :factories="{{ $factories }}"
        :customers="{{ $customers }}"
        :search-params="{{ json_encode($params ?: new \StdClass()) }}"
        :old-params="{{ json_encode(old() ?: new \StdClass()) }}">
      </search-manual-created-orders-form>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2">
      @if (count($params))
        @canSave(Auth::user(), route_relatively('order.order_input.index'))
        <add-order-manually-form
          route-action="{{ route('order.order_input.create') }}"
          :factories="{{ $factories }}"
          :customers="{{ $customers }}"
          :currencies="{{ $currencies }}"
          default-currency-code="{{ $default_currency_code }}"
          :search-params="{{ json_encode($params) }}"
          :old-params="{{ json_encode(! old('order_number') ? (old() ?: new \stdClass()) : new \stdClass()) }}">
        </add-order-manually-form>
        @endcanSave
      @endif
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
  <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $orders->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $orders->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-10 col-sm-10 col-xs-11 col-md-offset-1 col-sm-offset-1 horizontal-scroll-table">
    <table class="table table-color-bordered table-more-condensed" style="margin-bottom: 0">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('order.order_input.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>{{ __('view.order.order_list.order_number') }}</th>
          <th>{{ __('view.order.order_list.delivery_date') }}</th>
          <th>{{ __('view.master.products.product') }}</th>
          <th>{{ __('view.order.order_list.order_quantity') }}</th>
          <th>{{ __('view.order.order_list.order_unit') }}</th>
          <th>{{ __('view.order.order_list.order_amount') }}</th>
          <th>{{ __('view.master.global.currency_code') }}</th>
          <th>{{ __('view.order.order_list.base_plus_order_number') }}</th>
          <th>{{ __('view.order.order_list.end_user_order_number') }}</th>
          <th>{{ __('view.global.remark') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $o)
        <tr>
          <td>
            <edit-manual-created-order-form
              route-action="{{ route('order.order_input.update', $o->order_number) }}"
              :order="{{ $o }}"
              :factories="{{ $factories }}"
              :customers="{{ $customers }}"
              :currencies="{{ $currencies }}"
              :search-params="{{ json_encode($params) }}"
              :old-params="{{ json_encode(old('order_number') === $o->order_number ? (old() ?: new \stdClass()) : new \stdClass()) }}"
              :can-save-order="{{ json_encode(Auth::user()->canSave(route_relatively('order.order_input.index'))) }}">
            </edit-manual-created-order-form>
          </td>
          @canSave(Auth::user(), route_relatively('order.order_input.index'))
          <td>
            <delete-manual-created-order-form
              route-action="{{ route('order.order_input.delete', $o->order_number) }}"
              :order="{{ $o }}">
            </delete-manual-created-order-form>
          </td>
          @endcanSave
          <td>{{ $o->order_number }}</td>
          <td>{{ $o->delivery_date }}</td>
          <td class="text-left">{{ $o->product_name }}</td>
          <td class="text-right">{{ $o->order_quantity }}&nbsp;{{ $o->place_order_unit_code }}</td>
          <td class="text-right">{{ $o->formatOrderUnit() }}</td>
          <td class="text-right">{{ $o->formatOrderAmount() }}</td>
          <td>{{ $o->currency_code }}</td>
          <td>{{ $o->getBasePlusOrderNumber() }}</td>
          <td class="text-left">{{ $o->end_user_order_number }}</td>
          <td class="text-left">{!! nl2br(e($o->order_message), false) !!}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
