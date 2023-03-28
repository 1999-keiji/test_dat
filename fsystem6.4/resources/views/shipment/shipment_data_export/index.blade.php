@extends('layout')

@section('title')
{{ __('view.shipment.shipment_data_export.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.shipment_data_export.index') }}</li>
@endsection

@section('content')
<search-shipment-data-export-file
  action-export="{{ route('shipment.shipment_data_export.export') }}"
  :factories="{{ $factories }}"
  :customers="{{ $customers }}"
  :shipment-data-export-file-list="{{ json_encode($shipment_data_export_file->all()) }}"
  :errors="{{ $errors }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}">
</search-shipment-data-export-file>
@endsection
