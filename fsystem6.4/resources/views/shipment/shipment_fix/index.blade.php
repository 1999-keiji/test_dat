@extends('layout')

@section('title')
{{ __('view.shipment.shipment_fix.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.shipment_fix.index') }}</li>
@endsection

@section('styles')
  @parent

  <style>
    .action-button-area>button.btn.btn-lg {
      width: 85px;
      margin-top: 3px;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('shipment.shipment_fix.search') }}" method="POST">
    <search-shipment-fix-form
      :factories="{{ $factories }}"
      :customers="{{ $customers }}"
      :shipment-status-list="{{ json_encode($shipment_status->all()) }}"
      :search-params="{{ json_encode($params ?: new \stdClass()) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}">
    </search-shipment-fix-form>
    <div class="col-md-1 col-sm-2 col-xs-2 col-md-offset-1 action-button-area">
      <button class="btn btn-lg btn-default pull-left" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
      @canSave(Auth::user(), route_relatively('shipment.shipment_fix.index'))
        @if (count($orders) !== 0)
          <button id="shipment-fix-submit-button" class="btn btn-lg btn-default pull-left" type="button">
            <i class="fa fa-check"></i>{{ __('view.global.fix') }}
          </button>
        @endif
      @endcanSave
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<form id="shipment-fix-submit-form" action="{{ route('shipment.shipment_fix.fix') }}" method="POST">
  <p>
    <span class="search-result-count">{{ $orders->count() }}</span>
    {{ __('view.global.suffix_serach_result_count') }}
  </p>
  <div class="row header-fixed-table-row">
    <div class="fixed-table-head">
      <table class="table table-color-bordered table-more-condensed set-width-target">
        <thead>
          <tr>
            <th>
              <label>
                <input id="check-all"  type="checkbox" name="category_all">{{ __('view.global.all_check') }}
              </label>
            </th>
            <th>{{ __('view.shipment.global.shipment') }}{{ __('view.global.status') }}</th>
            <th>{{ __('view.order.order_list.order_number') }}</th>
            <th>{{ __('view.order.global.order') }}{{ __('view.global.status') }}</th>
            <th>{{ __('view.master.end_users.end_user') }}</th>
            <th>{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
            <th>{{ __('view.shipment.global.shipping_date') }}</th>
            <th>{{ __('view.shipment.global.delivery_date') }}</th>
            <th>{{ __('view.master.products.product') }}</th>
            <th>{{ __('view.shipment.shipment_fix.order_quantity') }}</th>
            <th>{{ __('view.order.order_list.order_amount') }}</th>
            <th>{{ __('view.master.global.currency_code') }}</th>
          </tr>
        </thead>
      </table>
    </div>
    <div class="fixed-table-body">
      <table class="table table-color-bordered table-more-condensed get-width-target">
        <tbody>
          @foreach ($orders as $o)
          <tr>
            @if ($o->hadBeenShipped())
              <td></td>
              <td>{{ $o->shipment_status->label() }}</td>
            @else
              <td>
                <input class="check-target" type="checkbox" name="order_numbers[]" value="{{ $o->order_number }}">
              </td>
              <td></td>
            @endif
            <td>{{ $o->order_number }}</td>
            <td>{{ $o->slip_status_type->label() }}</td>
            <td class="text-left">{{ $o->end_user_abbreviation }}</td>
            <td class="text-left">{{ $o->delivery_destination_abbreviation }}</td>
            <td>{{ $o->shipping_date }}</td>
            <td>{{ $o->delivery_date }}</td>
            <td class="text-left">{{ $o->product_name }}</td>
            <td class="text-right">{{ number_format($o->order_quantity) }}&nbsp;{{ $o->place_order_unit_code }}</td>
            <td class="text-right">{{ $o->formatOrderAmount() }}</td>
            <td>{{ $o->currency_code }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <input name="factory_code" type="hidden" value="{{ $params['factory_code'] }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
</form>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('#shipment-fix-submit-button').click(function () {
      if ($('input[name="order_numbers[]"]:checked').val() === undefined) {
        alert('出荷確定対象の注文を選択してください。')
        return
      }

      if (! confirm('選択された項目を出荷確定します。よろしいですか?')) {
        return
      }

      $('.alert').remove()
      $(this).prop('disabled', true)
      $('#shipment-fix-submit-form').submit()
    })
  </script>
@endsection
