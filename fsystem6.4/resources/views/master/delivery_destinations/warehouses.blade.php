@extends('layout')

@section('title')
{{ __('view.master.delivery_destinations.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.delivery_destinations.index') }}">{{ __('view.master.delivery_destinations.index') }}</a>
  </li>
  <li>{{ __('view.master.delivery_destinations.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.delivery_destinations.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<ul class="nav nav-tabs">
  <li><a href="{{ route('master.delivery_destinations.edit', $delivery_destination->delivery_destination_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li><a href="{{ route('master.delivery_destinations.factory_products', $delivery_destination->delivery_destination_code) }}">{{ __('view.master.products.product') }}</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="warehouse_tab">
    <div class="row">
      <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-5 col-sm-offset-5 col-xs-offset-6">
        @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
        <add-delivery-warehouse
          route-action="{{ route('master.delivery_warehouses.create') }}"
          delivery-destination-code="{{ $delivery_destination->delivery_destination_code }}"
          :warehouses="{{ $warehouses->toJson() }}"
          :delivery-lead-time="{{ $delivery_lead_time->toJson() }}"
          :shipment-lead-time-list="{{ $shipment_lead_time->toJson() }}"
          delivery-lead-time-old="{{ old('delivery_lead_time', '') }}">
        </add-delivery-warehouse>
        @endcanSave
      </div>
    </div>
    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
        <p>
          <span class="search-result-count">{{ $delivery_destination->delivery_warehouses->count() }}</span>{{ __('view.global.suffix_serach_result_count') }}
        </p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-sm-6 col-xs-10 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed">
          <thead>
            <tr>
              @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
              <th>{{ __('view.global.edit') }}</th>
              <th>{{ __('view.global.delete') }}</th>
              @endcanSave
              <th>{{ __('view.master.warehouses.warehouse') }}</th>
              <th>{{ __('view.master.delivery_warehouses.delivery_lead_time') }}</th>
              <th>{{ __('view.master.delivery_warehouses.shipment_lead_time') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($delivery_destination->delivery_warehouses as $dw)
            <tr>
              @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
              <td>
                <edit-delivery-warehouse
                  route-action="{{ route('master.delivery_warehouses.update', $dw->getJoinedPrimaryKeys()) }}"
                  :delivery-warehouse="{{ $dw->toJsonToEdit() }}"
                  :delivery-lead-time="{{ $delivery_lead_time->toJson() }}"
                  :shipment-lead-time-list="{{ $shipment_lead_time->toJson() }}">
                </edit-delivery-warehouse>
              </td>
              <td>
                <delete-form route-action="{{ route('master.delivery_warehouses.delete', $dw->getJoinedPrimaryKeys()) }}">
                </delete-form>
              </td>
              @endcanSave
              <td class="text-left">{{ $dw->warehouse->warehouse_abbreviation }}</td>
              <td class="{{ $dw->delivery_lead_time ? '' : 'danger' }}">{{ optional($dw->delivery_lead_time)->withSuffix() }}</td>
              <td>{{ $dw->shipment_lead_time->label() }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
