@extends('layout')

@section('title')
{{ __('view.master.warehouses.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.warehouses.index') }}">{{ __('view.master.warehouses.index') }}</a>
  </li>
  <li>{{ __('view.master.warehouses.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.warehouses.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<ul class="nav nav-tabs">
  <li><a href="{{ route('master.warehouses.edit', $warehouse->warehouse_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.factories.factory') }}</a></li>
  <li><a href="{{ route('master.warehouses.delivery_warehouses', $warehouse->warehouse_code) }}">{{ __('view.master.delivery_destinations.delivery_destination') }}</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="factory_warehouse_tab">
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-9 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed">
          <thead>
            <tr>
              <th>{{ __('view.master.factories.factory_code') }}</th>
              <th>{{ __('view.master.factories.factory_name') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($warehouse->factory_warehouses as $fw)
            <tr>
              <td class="text-center">{{ $fw->factory->factory_code }}</td>
              <td class="text-left">{{ $fw->factory->factory_abbreviation }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
