@extends('layout')

@section('title')
{{ __('view.shipment.form_output.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.form_output.index') }}</li>
@endsection

@section('styles')
  @parent

  <style>
    .modal-dialog {
      width: 90%;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('shipment.form_output.search') }}" method="POST">
    <search-form-output-form
      :factories="{{ $factories }}"
      :customers="{{ $customers }}"
      :output-file-list="{{ json_encode($output_file->all()) }}"
      :print-state-list="{{ json_encode($print_state->all()) }}"
      :search-params="{{ json_encode($params ?: new \stdClass()) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}">
    </search-form-output-form>
    <div class="col-md-3 col-sm-3 col-xs-4">
      <button class="btn btn-lg btn-default pull-left" name="action" value="search" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
      <button id="download-pdf" class="btn btn-lg btn-default pull-left" type="button">
        <i class="fa fa-download"></i> {{ __('view.global.pdf_download') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ count($grouped_orders) }}</span>
      {{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
</div>

<div class="row">
  <div class="scroll-table-row form-output-row">
    <form id="download-pdf-form" action="{{ route('shipment.form_output.download') }}" method="POST">
      <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1 list-head">
        <table class="table table-color-bordered table-more-condensed set-width-target">
          <thead>
            <tr>
              <th>
                <input id="check-all" type="checkbox"> {{ __('view.global.all_check') }}
              </th>
              <th>{{ __('view.global.detail') }}</th>
              <th>{{ __('view.global.status') }}</th>
              <th>{{ __('view.shipment.global.shipping_date') }}</th>
              <th>{{ __('view.shipment.global.delivery_date') }}</th>
              <th>{{ __('view.master.end_users.end_user') }}</th>
              <th>{{ __('view.master.delivery_destinations.delivery_destination') }}</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1 list-body">
        <table class="table table-color-bordered table-more-condensed get-width-target">
          <tbody>
            @foreach ($grouped_orders as $group)
            <tr>
              <td>
                <input class="check-target" type="checkbox" name="group_check[]" value="{{ $group->orders->pluck('order_number')->implode('-') }}">
              </td>
              <td>
                <slip-detail-information
                  :orders="{{ json_encode($group->orders) }}">
                </slip-detail-information>
              </td>
              <td>{{ $group->print_state }}</td>
              <td>{{ $group->shipping_date }}</td>
              <td>{{ $group->delivery_date }}</td>
              <td class="text-left">{{ $group->end_user_abbreviation }}</td>
              <td class="text-left">{{ $group->delivery_destination_abbreviation }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <input type="hidden" name="output_file" value="{{ $params['output_file'] }}">
      {{ csrf_field() }}
      {{ method_field('POST') }}
    </form>
  </div>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('#download-pdf').on('click', function () {
      if ($('input[name="group_check[]"]:checked').val() === undefined) {
        alert('出荷情報を選択してください。')
      } else {
        $('#download-pdf-form').submit()
      }
    })
  </script>
@endsection
