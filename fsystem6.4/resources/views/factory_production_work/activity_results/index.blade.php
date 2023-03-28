@extends('layout')

@section('title')
{{ __('view.factory_production_work.activity_results.index') }}
@endsection

@section('menu-section')
{{ __('view.index.factory-production-work') }}
@endsection

@section('breadcrumbs')
<li>{{ __('view.factory_production_work.activity_results.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('factory_production_work.activity_results.search') }}" method="POST">
    <div class="col-md-7 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <select-factory-species
        :factories="{{ $factories }}"
        working-date="{{ old('working_date', $params['working_date'] ?? null) }}"
        selected-factory-code="{{ old('factory_code', $params['factory_code'] ?? null) }}"
        selected-factory-species-code="{{ old('factory_species_code', $params['factory_species_code'] ?? null) }}">
      </select-factory-species>
    </div>
    <div class="col-md-3 col-md-pull-1 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-2">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-10 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          <th>{{ __('view.master.species.species') }}</th>
          <th>{{ __('view.master.factory_species.factory_species') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($activity_results as $ar)
        <tr>
          <td>
            <a
              class="btn btn-sm btn-info"
              href="{{ route('factory_production_work.activity_results.edit', implode('|', [$ar->factory_code, $ar->factory_species_code])) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          <td class="text-left">{{ $ar->species_name }}</td>
          <td class="text-left">{{ $ar->factory_species_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
