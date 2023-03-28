@extends('master.factories.edit')

@section('nav-tabs')
  <li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li><a href="{{ route('master.factories.beds', $factory->factory_code) }}">{{ __('view.master.factories.layout') }}</a></li>
  <li><a href="{{ route('master.factories.warehouses', $factory->factory_code) }}">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li><a href="{{ route('master.factories.panels', $factory->factory_code) }}">{{ __('view.master.factories.panel') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.factories.pattern') }}</a></li>
  <li><a href="{{ route('master.factories.jccores', $factory->factory_code) }}">{{ __('view.master.factories.jccores') }}</a></li>
@endsection

@section('edit_content')
<edit-factory-cycle-pattern
  :factory="{{ $factory }}"
  :day-of-the-weeks="{{ json_encode($working_date->getDayOfTheWeeks()) }}"
  :working-day-of-the-weeks="{{ json_encode($factory->getWorkingDayOfTheWeeks()) }}"
  :factory-cycle-patterns="{{ $factory->factory_cycle_patterns }}"
  action-update="{{ route('master.factories.cycle_patterns.update', $factory->factory_code) }}"
  action-delete="{{ route('master.factories.cycle_patterns.delete', [$factory->factory_code, $factory->factory_code.'|']) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}"
  :can-save-factory="{{ json_encode(Auth::user()->canSave(route_relatively('master.factories.index'))) }}">
</edit-factory-cycle-pattern>
@endsection
