@extends('master.factories.edit')

@section('nav-tabs')
  <li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li><a href="{{ route('master.factories.beds', $factory->factory_code) }}">{{ __('view.master.factories.layout') }}</a></li>
  <li><a href="{{ route('master.factories.warehouses', $factory->factory_code) }}">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.factories.panel') }}</a></li>
  <li><a href="{{ route('master.factories.cycle_patterns', $factory->factory_code) }}">{{ __('view.master.factories.pattern') }}</a></li>
  <li><a href="{{ route('master.factories.jccores', $factory->factory_code) }}">{{ __('view.master.factories.jccores') }}</a></li>
@endsection

@section('edit_content')
<form id="add-factory-panels-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.factories.panels.create', $factory->factory_code) }}" method="POST">
  @canSave(Auth::user(), route_relatively('master.factories.index'))
  <div class="row">
    <div class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="number_of_holes" class="col-md-4 col-sm-4 control-label required">
          {{ __('view.master.factories.number_of_holes') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
        <input
          id="number_of_holes"
          name="number_of_holes"
          class="form-control text-right ime-inactive {{ has_error('number_of_holes') }}"
          maxlength="3"
          type="text"
          value="{{ old('number_of_holes', '') }}"
          required>
        </div>
      </div>
    </div>
    <div class="col-md-7 col-sm-7 col-xs-8">
      <button class="btn btn-default btn-lg save-button pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.add') }}
      </button>
    </div>
  </div>
  @endcanSave
  {{ method_field('POST') }}
  {{ csrf_field() }}
</form>

<div class="row">
  <div class="col-md-4 col-sm-4 col-xs-8 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          @canSave(Auth::user(), route_relatively('master.factories.index'))
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>{{ __('view.master.factories.panel') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($factory_panels as $fp)
        <tr>
          @canSave(Auth::user(), route_relatively('master.factories.index'))
          <td>
            @if ($fp->isDeletable())
            <delete-form route-action="{{ route('master.factories.panels.delete', [$factory->factory_code, $fp->getJoinedPrimaryKeys()]) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">&nbsp;{{ $fp->number_of_holes }}&nbsp;穴</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
