@extends('master.factories.edit')

@section('nav-tabs')
  <li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li><a href="{{ route('master.factories.beds', $factory->factory_code) }}">{{ __('view.master.factories.layout') }}</a></li>
  <li><a href="{{ route('master.factories.warehouses', $factory->factory_code) }}">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li><a href="{{ route('master.factories.panels', $factory->factory_code) }}">{{ __('view.master.factories.panel') }}</a></li>
  <li><a href="{{ route('master.factories.cycle_patterns', $factory->factory_code) }}">{{ __('view.master.factories.pattern') }}</a></li>
  <li class="active"><a href="{{ route('master.factories.jccores', $factory->factory_code) }}">{{ __('view.master.factories.jccores') }}</a></li>
@endsection

@section('edit_content')
<div>
  <form active="{{ route('master.factories.jccores.update', $factory->factory_code) }}" id="delete-factory-jccores-form" enctype="multipart/form-data" method="POST" class="form-horizontal basic-form save-data-form">
    <div class="row">
      <div class="col-md-4 col-sm-4 col-xs-4 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="conversion-code" class="col-md-5 col-sm-5 control-label">
            工場略称
          </label>
          <div class="col-md-7 col-sm-7">
            {{ $factory->factory_abbreviation }}
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        @canSave(Auth::user(), route_relatively('master.factories.index'))
        <button class="btn btn-default btn-lg save-button pull-right save-data" type="button">
          <i class="fa fa-save"></i> {{ __('view.global.save') }}
        </button>
        @endcanSave
      </div>
      <div class="col-md-4 col-sm-4 col-xs-4 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="conversion_code" class="col-md-5 col-sm-5 control-label required">
            原価計算用工場コード
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <input id="conversion_code" class="form-control" name="conversion_code" type="text" value="">
          </div>
        </div>
      </div>
    </div>
    <input name="_token" type="hidden" value="csrf">
    <input name="_method" type="hidden" value="POST">
  </form>
</div>
{{-- <edit-factory-jccores
:factory="{{ $factory }}"
:old-params="{{ json_encode(old() ?: new \stdClass()) }}"
:errors="{{ $errors }}"
action-update="{{ route('master.factories.jccores.update', $factory->factory_code) }}"
:can-save-factory="{{ json_encode(Auth::user()->canSave(route_relatively('master.factories.index'))) }}"
>
</edit-factory-jccores> --}}
@endsection

