@extends('layout')

@section('title')
{{ __('view.shipment.invoices.export') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.invoices.export') }}</li>
@endsection

@section('content')
<export-invoice-form
  action-export="{{ route('shipment.invoices.export') }}"
  :factories="{{ $factories }}"
  :customers="{{ $customers }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}">
</export-invoice-form>
@endsection
