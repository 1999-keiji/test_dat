@extends('layout')

@section('title')
{{ __('view.master.factory_species.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
<li><a href="{{ route('master.factories.index') }}">{{ __('view.master.factories.index') }}</a></li>
<li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.factories.edit') }}</a></li>
<li>{{ __('view.master.factory_species.index') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factories.edit', $factory->factory_code) }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  @canSave(Auth::user(), route_relatively('master.factories.index'))
  <div class="col-md-5 col-sm-5 col-xs-6">
    <a class="btn btn-default btn-lg pull-right" href="{{ route('master.factory_species.add', $factory->factory_code) }}">
      <i class="fa fa-plus"></i> {{ __('view.global.add') }}
    </a>
  </div>
  @endcanSave
</div>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered">
      <tr>
        <th>{{ __('view.master.factories.factory_code') }}</th>
        <td><label>{{ $factory->factory_code }}</label></td>
        <th>{{ __('view.master.factories.factory_name') }}</th>
        <td><label>{{ $factory->factory_abbreviation }}</label></td>
      </tr>
    </table>
  </div>
</div>

<div class="row factory-species-row">
  <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1 fs-list-head">
    <table class="table table-color-bordered table-more-condensed set-width-target">
      <thead>
        <tr>
          <th>{{ __('view.global.select') }}</th>
          <th>@sortablelink('species.species_name', __('view.master.species.species_name'))</th>
          <th>@sortablelink('factory_species_code', __('view.master.factory_species.factory_species_code'))</th>
          <th>@sortablelink('factory_species_name', __('view.master.factory_species.factory_species_name'))</th>
        </tr>
      </thead>
    </table>
  </div>
  <form action="{{ route('master.factory_species.edit', [$factory->factory_code, $factory->factory_code.'|']) }}" method="GET">
    <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1 fs-list-body">
      <table class="table table-color-bordered table-more-condensed get-width-target">
        <tbody>
          @foreach ($factory_species_list as $fs)
          <tr>
            <td>
              <input
                type="radio"
                name="factory_species_code"
                value="{{ $fs->factory_species_code }}"
                @if ($factory_species->factory_species_code === $fs->factory_species_code)
                checked
                @endif>
            </td>
            <td class="text-left">{{ $fs->species->species_name }}</td>
            <td class="text-left">{{ $fs->factory_species_code }}</td>
            <td class="text-left">{{ $fs->factory_species_name }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </form>
</div>

<div class="row row-pattern">
  <form class="form-horizontal basic-form save-data-form" action="{{ route('master.factory_species.update', [$factory->factory_code, $factory_species->getJoinedPrimaryKeys()]) }}" method="POST">
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        @canSave(Auth::user(), route_relatively('master.factories.index'))
          @if ($factory_species->isDeletable())
          <delete-form
            route-action="{{ route('master.factory_species.delete', [$factory->factory_code, $factory_species->getJoinedPrimaryKeys()]) }}"
            :is-large-button="{{ json_encode(true) }}">
          </delete-form>
          @endif
        <button class="btn btn-default btn-lg pull-right save-data" type="submit">
          <i class="fa fa-save"></i> {{ __('view.global.save') }}
        </button>
        @endcanSave
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            {{ __('view.master.species.species') }}
          </label>
          <div class="col-md-7 col-sm-7">
            <label>{{ $factory_species->species->species_name }}</label>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            {{ __('view.master.factory_species.factory_species_code') }}
          </label>
          <div class="col-md-7 col-sm-7">
            <label>{{ $factory_species->factory_species_code }}</label>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="factory_species_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            {{ __('view.master.factory_species.factory_species_name') }}
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <input id="factory_species_name" class="form-control ime-active" maxlength="30" name="factory_species_name" type="text" value="{{ old('factory_species_name', $factory_species->factory_species_name) }}" required>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="weight" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            {{ __('view.master.factory_species.weight') }}
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <input id="weight" class="form-control ime-inactive" maxlength="9" name="weight" type="number" value="{{ old('weight', $factory_species->weight) }}" required>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="delivery_destination" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            {{ __('view.master.global.can_display') }}
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            @foreach ($can_display_list as $label => $value)
            <label class="radio-inline">
              <input type="radio" name="can_select_on_simulation" id="can_select_on_simulation-{{ $value }}" value="{{ $value }}" {{ is_checked($value, old('can_select_on_simulation', $factory_species->can_select_on_simulation)) }}>{{ $label }}
            </label>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            {{ __('view.global.remark') }}
          </label>
          <div class="col-md-7 col-sm-7">
            <input id="remark" class="form-control ime-active" maxlength="50" name="remark" value="{{ old('remark', $factory_species->remark) }}" type="text">
          </div>
        </div>
      </div>
    </div>

    <add-factory-species
      :factory-panels="{{ $factory->factory_panels }}"
      :factory-cycle-patterns="{{ $factory->factory_cycle_patterns }}"
      :current-factory-growing-stages="{{ $factory_species->factory_growing_stages }}"
      :growing-stage="{{ $growing_stage->toJson() }}"
      :can-save-factory="{{ json_encode(Auth::user()->canSave(route_relatively('master.factories.index'))) }}">
    </add-factory-species>
    <input type="hidden" name="updated_at" value="{{ $factory_species->updated_at }}">
    {{ csrf_field() }}
    {{ method_field('PATCH') }}

    @canSave(Auth::user(), route_relatively('master.products.index'))
    <input id="can-save-data" type="hidden" value="1">
    @else
    <input id="can-save-data" type="hidden" value="0">
    @endcanSave
  </form>
</div>
@endsection
