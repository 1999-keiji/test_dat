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
              <input type="radio" name="factory_species_code" value="{{ $fs->factory_species_code }}">
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
@endsection
