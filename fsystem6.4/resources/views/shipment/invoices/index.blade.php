@extends('layout')

@section('title')
{{ __('view.shipment.invoices.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.invoices.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('shipment.invoices.search') }}" method="POST" >
    <div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <colgroup>
          <col class="col-md-2 col-sm-2 col-xs-2">
          <col class="col-md-4 col-sm-4 col-xs-4">
          <col class="col-md-2 col-sm-2 col-xs-2">
          <col class="col-md-4 col-sm-4 col-xs-4">
        </colgroup>
        <tbody>
          <tr>
            <th>
              {{ __('view.master.factories.factory') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <select class="form-control {{ has_error('factory_code') }}" name="factory_code">
                <option value=""></option>
                @foreach ($factories as $f)
                <option value="{{ $f->factory_code }}" {{ is_selected($f->factory_code, $params['factory_code'] ?? '') }}>
                  {{ $f->factory_abbreviation }}
                </option>
                @endforeach
              </select>
            </td>
            <th>
                {{ __('view.master.customers.customer') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <select class="form-control {{ has_error('customer_code') }}" name="customer_code">
                <option value=""></option>
                @foreach ($customers as $c)
                <option value="{{ $c->customer_code }}" {{ is_selected($c->customer_code, $params['customer_code'] ?? '') }}>
                  {{ $c->customer_abbreviation }}
                </option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr>
            <th>{{ __('view.global.year_month') }}</th>
            <td>
              <year-monthpicker-ja
                name="delivery_month"
                value="{{ old('delivery_month', $params['delivery_month'] ?? '') }}"
                :allow-empty="{{ json_encode(true) }}" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-2 col-sm-3 col-xs-4">
      <button class="btn btn-lg btn-default btn-excel-download pull-right" type="submit">
        <i class="fa fa-search"></i>&nbsp;検索
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
      <span class="search-result-count">{{ $invoices->count() }}</span>
      {{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          @canSave(Auth::user(), route_relatively('shipment.invoices.index'))
          <th>{{ __('view.global.operate') }}</th>
          @endcanSave
          <th>@sortablelink('orders.delivery_month', __('view.global.year_month'))</th>
          <th>{{ __('view.global.fixed_by') }}</th>
          <th>{{ __('view.global.fixed_at') }}</th>
          <th>{{ __('view.shipment.invoices.order_quantity') }}</th>
          <th>{{ __('view.shipment.invoices.order_amount') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($invoices as $i)
        <tr>
          @canSave(Auth::user(), route_relatively('shipment.invoices.index'))
          <td>
            @if ($i->hasFixed())
            <form action="{{ route('shipment.invoices.cancel', $i->invoice_number) }}" method="POST">
              <button class="btn btn-sm btn-danger cancel-invoice" type="button">
                {{ __('view.global.unlock') }}
              </button>
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
            @else
            <form action="{{ route('shipment.invoices.fix') }}" method="POST">
              <button class="btn btn-sm btn-info fix-invoice" type="button">
                {{ __('view.global.fix') }}
              </button>
              <input name="factory_code" type="hidden" value="{{ $i->factory_code }}">
              <input name="customer_code" type="hidden" value="{{ $i->customer_code }}">
              <input name="delivery_month" type="hidden" value="{{ $i->delivery_month }}">
              {{ csrf_field() }}
              {{ method_field('POST') }}
            </form>
            @endif
          </td>
          @endcanSave
          <td>{{ $i->delivery_month }}</td>
          <td
            @if ($i->hasFixed())
            class="text-left"
            @endif>{{ $i->hasFixed() ? $i->fix_user_name : '-' }}</td>
          <td>{{ $i->hasFixed() ? $i->fixed_at : '-' }}</td>
          <td class="text-right">{{ number_format($i->hasFixed() ? $i->order_quantity : $i->orders_order_quantity) }}</td>
          <td class="text-right">￥{{ number_format($i->hasFixed() ? $i->order_amount : $i->orders_order_amount) }}</td>
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
    $('.fix-invoice').click(function () {
      if (! confirm('選択された年月の請求書を確定します。よろしいですか?')) {
        return
      }

      $('.alert').remove()
      $(this).prop('disabled', true)
      $(this).parent().submit()
    })

    $('.cancel-invoice').click(function () {
      if (! confirm('選択された年月の請求書の確定を解除します。よろしいですか?')) {
        return
      }

      $('.alert').remove()
      $(this).prop('disabled', true)
      $(this).parent().submit()
    })
  </script>
@endsection
