@extends('master.factories.edit')

@section('nav-tabs')
  <li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.factories.layout') }}</a></li>
  <li><a href="{{ route('master.factories.warehouses', $factory->factory_code) }}">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li><a href="{{ route('master.factories.panels', $factory->factory_code) }}">{{ __('view.master.factories.panel') }}</a></li>
  <li><a href="{{ route('master.factories.cycle_patterns', $factory->factory_code) }}">{{ __('view.master.factories.pattern') }}</a></li>
  <li><a href="{{ route('master.factories.jccores', $factory->factory_code) }}">{{ __('view.master.factories.jccores') }}</a></li>
@endsection

@section('edit_content')
<edit-factory-layout
  :factory="{{ $factory }}"
  :factory-columns="{{ $factory_columns }}"
  :factory-beds="{{ $factory_beds->groupBy(['floor', 'row']) }}"
  :circulations="{{ json_encode($factory_columns->circulations()) }}"
  :default-x-coordinate-panel="{{ json_encode($factory_beds->isNotEmpty() ? head($factory_beds->mode('x_coordinate_panel')) : 0) }}"
  :default-y-coordinate-panel="{{ json_encode($factory_beds->isNotEmpty() ? head($factory_beds->mode('y_coordinate_panel')) : 0) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}"
  action-update="{{ route('master.factories.beds.update', $factory->factory_code) }}"
  :can-save-factory="{{ json_encode(Auth::user()->canSave(route_relatively('master.factories.index'))) }}">
</edit-factory-layout>
@endsection
