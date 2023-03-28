@extends('layout')

@section('title')
{{ __('view.shipment.collection_request.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.collection_request.index') }}</li>
@endsection

@section('styles')
  @parent

  <style>
    .modal-dialog {
      width: 95%;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('shipment.collection_request.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <search-collection-request
        :factories="{{ $factories }}"
        :customers="{{ $customers }}"
        :transport-companies="{{ $transport_companies }}"
        :search-params="{{ json_encode($params) }}"
        :old-params="{{ json_encode(old()) }}">
      </search-collection-request>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-3">
      <button
        id="download-excel"
        class="btn btn-default pull-right btn-lg"
        type="button"
        @if (count($grouped_orders) === 0)
        disabled
        @endif>
        <i class="fa fa-download"></i> {{ __('view.global.excel_download') }}
      </button>
      <button class="btn btn-default btn-default btn-lg pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p><span class="search-result-count">{{ count($grouped_orders)}}</span>{{ __('view.global.suffix_serach_result_count') }}</p>
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th class="col-md-1 col-sm-1 col-xs-1">
            <input id="check-all" type="checkbox">{{ __('view.global.all_check') }}
          </th>
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.detail') }}</th>
          <th class="col-md-4 col-sm-4 col-xs-4">{{ __('view.shipment.global.shipping_date') }}</th>
          <th class="col-md-3 col-sm-3 col-xs-3">{{ __('view.master.transport_companies.transport_company') }}</th>
          <th class="col-md-3 col-sm-3 col-xs-3">{{ __('view.master.transport_companies.collection_time') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($grouped_orders as $group)
        <tr>
          <td>
            <input class="check-target" type="checkbox" value="{{ $group->orders->pluck('order_number')->implode('-') }}">
          </td>
          <td>
            <collection-request-detail-modal
              route-action="{{ route('shipment.collection_request.save') }}"
              :grouped-order="{{ json_encode($group) }}"
              :transport-companies="{{ $transport_companies }}">
            </collection-request-detail-modal>
          </td>
          <td>{{ $group->shipping_date }}</td>
          <td class="text-left">{{ $group->transport_company_abbreviation }}</td>
          <td class="text-left">{{ $group->collection_time }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<form id="download-excel-form" class="form-horizontal basic-form export-data-form" action="{{ route('shipment.collection_request.export') }}" method="POST">
  {{ csrf_field() }}
  {{ method_field('POST') }}
</form>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('#download-excel').on('click', function () {
      if ($('input.check-target:checked').val() === undefined) {
        alert('注文情報を選択してください。')
        return
      }

      if (! confirm('Excelをダウンロードしますか?')) {
        return
      }

      var $form = $('#download-excel-form')
      $('input.check-target:checked').each(function () {
        $form.append(
          $('<input>').attr('name', 'group_check[]').attr('type', 'hidden').val($(this).val())
        )
      })

      $form.submit()
      $('input[name="group_check[]"]').remove();
    })
  </script>
@endsection
