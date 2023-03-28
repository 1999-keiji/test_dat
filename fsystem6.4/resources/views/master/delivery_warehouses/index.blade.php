@extends('layout')

@section('title')
{{ __('view.master.lead_time.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.lead_time.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.lead_time.search') }}" method="POST">
    <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.delivery_destinations.delivery_destination_code') }}</th>
          <td>
            <input class="form-control" maxlength="{{ $delivery_destination_code->getMaxLength() }}" name="delivery_destination_code" value="{{ old('delivery_destination_code', $params['delivery_destination_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.delivery_destinations.index_delivery_destination_name') }}</th>
          <td>
            <input class="form-control" maxlength="50" name="delivery_destination_name" value="{{ old('delivery_destination_name', $params['delivery_destination_name'] ?? '') }}">
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.warehouses.warehouse_code') }}</th>
          <td>
            <input class="form-control" maxlength="{{ $warehouse_code->getMaxLength() }}" name="warehouse_code" value="{{ old('warehouse_code', $params['warehouse_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.warehouses.warehouse_name') }}</th>
          <td>
            <input class="form-control" maxlength="50" name="warehouse_name" value="{{ old('warehouse_name', $params['warehouse_name'] ?? '') }}">
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
          <td>
            <label class="radio-inline">
              <input type="radio" name="delivery_lead_time" value="1" {{ is_checked(1, $params['delivery_lead_time'] ?? 1) }}>すべて
            </label>
            <label class="radio-inline">
              <input type="radio" name="delivery_lead_time" value="0" {{ is_checked(0, $params['delivery_lead_time'] ?? 1) }}>未設定
            </label>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-2 col-sm-offset-2">
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
      <span class="search-result-count">{{ $delivery_warehouses->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $delivery_warehouses->appends(request(['sort', 'order']))->links() }}
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          @canSave(Auth::user(), route_relatively('master.lead_time.index'))
          <th>{{ __('view.global.edit') }}</th>
          @endcanSave
          <th>@sortablelink('delivery_warehouses.delivery_destination_code', __('view.master.delivery_destinations.delivery_destination_code'))</th>
          <th>@sortablelink('delivery_destinations.delivery_destination_abbreviation', __('view.master.delivery_destinations.index_delivery_destination_name'))</th>
          <th>{{ __('view.master.warehouses.warehouse_code') }}</th>
          <th>{{ __('view.master.warehouses.warehouse_name') }}</th>
          <th>{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
          <th>{{ __('view.master.delivery_warehouses.shipment_lead_time') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($delivery_warehouses as $dw)
        <tr>
          @canSave(Auth::user(), route_relatively('master.lead_time.index'))
          <td>
            <edit-delivery-warehouse
              route-action="{{ route('master.delivery_warehouses.update', $dw->getJoinedPrimaryKeys()) }}"
              :delivery-warehouse="{{ $dw->toJsonToEdit() }}"
              :delivery-lead-time="{{ $delivery_lead_time->toJson() }}"
              :shipment-lead-time-list="{{ $shipment_lead_time->toJson() }}">
            </edit-delivery-warehouse>
          </td>
          @endcanSave
          <td class="text-left">{{ $dw->delivery_destination_code }}</td>
          <td class="text-left">{{ $dw->delivery_destination_abbreviation }}</td>
          <td class="text-left">{{ $dw->warehouse_code }}</td>
          <td class="text-left">{{ $dw->warehouse_abbreviation }}</td>
          <td class="{{ $dw->delivery_lead_time ? '' : 'danger' }}">{{ optional($dw->delivery_lead_time)->withSuffix() }}</td>
          <td>
           {{ $dw->shipment_lead_time->label() }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
