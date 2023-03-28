@extends('layout')

@section('title')
{{ __('view.order.order_list.index') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.order.order_list.index') }}</li>
@endsection

@section('styles')
  @parent

  <style type="text/css">
    table.table>tbody>tr.active>td {
      background-color: #d3d3d3;
    }
  </style>
@endsection

@section('content')
<search-orders-form
  search-orders-action="{{ route('order.order_list.search') }}"
  export-orders-action="{{ route('order.order_list.export') }}"
  match-orders-action="{{ route('order.order_list.match') }}"
  save-slip-action="{{ route('order.order_list.save-slip') }}"
  :factories="{{ $factories }}"
  :customers="{{ $customers }}"
  :allocation-status-list="{{ json_encode($allocation_status->all()) }}"
  :shipment-status-list="{{ json_encode($shipment_status->all()) }}"
  :search-params="{{ json_encode($params ?: new \stdClass()) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :currencies="{{ $currencies }}"
  default-currency-code="{{ $default_currency_code }}"
  :can-save-order="{{ json_encode(Auth::user()->canSave(route_relatively('order.order_list.index'))) }}">
</search-orders-form>

@if (count($params) !== 0)
<div class="row">
  <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
      <p>
        <span class="search-result-count">{{ $orders->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
      </p>
    </div>
    <div class="col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1">
      {{ $orders->appends(request(['sort', 'order']))->links() }}
    </div>
  </div>

  <div class="row">
    <table class="table table-color-bordered table-more-condensed sticky-table">
      <tbody>
        <tr class="header">
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('order.order_list.index'))
          <th>{{ __('view.global.cancel') }}</th>
          @endcanSave
          <th>{{ __('view.order.order_list.received_date') }}</th>
          <th>{{ __('view.order.order_list.order_number') }}</th>
          <th>@sortablelink('orders.process_class', __('view.order.order_list.status'))</th>
          @canSave(Auth::user(), route_relatively('order.order_list.index'))
          <th>{{ __('view.global.fix') }}<br>{{ __('view.order.order_list.link') }}</th>
          @endcanSave
          <th>@sortablelink('orders.delivery_date', __('view.order.order_list.delivery_date'))</th>
          <th>{{ __('view.master.end_users.end_user') }}</th>
          <th>{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
          <th>@sortablelink('orders.product_code', __('view.master.products.product'))</th>
          <th>{{ __('view.order.order_list.order_quantity') }}</th>
          <th>{{ __('view.order.order_list.order_unit') }}</th>
          <th>{{ __('view.order.order_list.order_amount') }}</th>
          <th>{{ __('view.master.global.currency_code') }}</th>
          <th>{{ __('view.order.order_list.base_plus_order_number') }}</th>
          <th>{{ __('view.master.end_users.end_user') }}<br>{{ __('view.order.order_list.order_number') }}</th>
          <th>{{ __('view.shipment.product_allocations.allocate') }}{{ __('view.global.status') }}</th>
          <th>{{ __('view.shipment.global.shipment') }}{{ __('view.global.status') }}</th>
          <th>{{ __('view.global.remark') }}</th>
          <th>{{ __('view.order.order_list.last_updated_by') }}</th>
          <th>{{ __('view.order.order_list.last_updated_at') }}</th>
        </tr>
        @foreach ($orders as $o)
        <tr
          @if ($o->isCanceledOrder() || $o->factory_cancel_flag)
          class='active'
          @endif>
          <td>
            @if (! $o->hadBeenShipped())
            <a class="btn btn-sm btn-info right-margined-btn" href="{{ route('order.order_list.edit', $o->order_number) }}">{{ __('view.global.edit') }}</a>
            @else
            <a class="btn btn-sm btn-primary right-margined-btn" href="{{ route('order.order_list.edit', $o->order_number) }}">{{ __('view.global.reference') }}</a>
            @endif
          </td>
          @canSave(Auth::user(), route_relatively('order.order_list.index'))
          <td>
            @if ($o->isCancelable())
            <form action="{{ route('order.order_list.cancel', $o->order_number) }}" method="POST">
              <button class="btn btn-sm btn-danger right-margined-btn cancel-order" type="button">{{ __('view.global.cancel') }}</button>
              <input name="updated_at" type="hidden" value="{{ $o->updated_at }}">
              {{ csrf_field() }}
              {{ method_field('PATCH') }}
            </form>
            @endif
          </td>
          @endcanSave
          <td>{{ $o->received_date }}</td>
          <td>{{ $o->order_number }}</td>
          <td class="text-left">{{ $o->getProcessClassLabel() }}:&nbsp;{{ $o->getOrderStatus() }}</td>
          @canSave(Auth::user(), route_relatively('order.order_list.index'))
          <td>
            @if ($o->isLinkableTemporaryOrder())
            <link-with-fixed-order
              :factory="{{ $factory }}"
              :customer="{{ $customer }}"
              :order="{{ $o }}"
              route-action="{{ route('order.order_list.link', $o->order_number) }}"
              :error-messages="{{ json_encode(config('operation.order.order_list.link')) }}">
            </link-with-fixed-order>
            @endif
            @if ($o->isLinkCancelableFixedOrder())
            <cancel-link
              :temporary-order="{{ $o->getLinkedTemporaryOrder() }}"
              :fixed-orders="{{ $o->getLinkedTemporaryOrder()->getLinkedFixedOrders() }}"
              route-action="{{ route('order.order_list.link.cancel', $o->getLinkedTemporaryOrder()->order_number) }}">
            </cancel-link>
            @endif
          </td>
          @endcanSave
          <td>{{ $o->delivery_date }}</td>
          <td class="text-left">{{ $o->end_user_abbreviation }}</td>
          <td class="text-left">{{ $o->delivery_destination_abbreviation }}</td>
          <td class="text-left">{{ $o->product_name }}</td>
          <td class="text-right">{{ number_format($o->order_quantity) }}&nbsp;{{ $o->place_order_unit_code }}</td>
          <td class="text-right">{{ $o->formatOrderUnit() }}</td>
          <td class="text-right">{{ $o->formatOrderAmount() }}</td>
          <td>{{ $o->currency_code }}</td>
          <td>{{ $o->getBasePlusOrderNumber() }}</td>
          <td class="text-left">{{ $o->end_user_order_number }}</td>
          <td>{{ $o->allocation_status->label() }}</td>
          <td>
            @if ($o->hadBeenShipped())
            {{ $o->shipment_status->label() }}
            @endif
          </td>
          <td class="text-left">{{ $o->order_message }}</td>
          <td class="text-left">{{ $o->updated_by }}</td>
          <td>{{ $o->updated_at }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('.cancel-order').click(function () {
      if (confirm('工場キャンセルしますか?')) {
        $(this).parent('form').submit()
      }
    })
  </script>
@endsection
