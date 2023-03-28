@extends('layout')

@section('title')
{{ __('view.master.species.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.species.index') }}">{{ __('view.master.species.index') }}</a>
  </li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.species.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.species.species_code') }}</th>
          <td>
            <input class="form-control" maxlength="{{ $species_code->getMaxLength() }}" name="species_code" value="{{ old('species_code', $params['species_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.species.species_name') }}</th>
          <td>
            <input class="form-control" maxlength="20" name="species_name" value="{{ old('species_name', $params['species_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.species.index'))
      <add-species
        route-action="{{ route('master.species.create') }}"
        :species-code="{{ $species_code->toJson() }}"
        :category-code="{{ $category_code->toJson() }}">
      </add-species>
      @endcanSave
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params))
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $species_list->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-4 col-sm-4 col-md-offset-2 col-sm-offset-2">
    {{ $species_list->appends(request(['sort', 'order']))->links() }}
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.species.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('species_code', __('view.master.species.species_code'))</th>
          <th>@sortablelink('species_name', __('view.master.species.species_name'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($species_list as $s)
        <tr>
          <td>
            <edit-species
              route-action="{{ route('master.species.update', $s->species_code) }}"
              :species="{{ $s }}"
              :species-converters="{{ $s->species_converters->toJson() }}"
              :category-code="{{ $category_code->toJson() }}"
              :can-save-species="{{ json_encode(Auth::user()->canSave(route_relatively('master.species.index'))) }}">
            </edit-species>
          </td>
          @canSave(Auth::user(), route_relatively('master.species.index'))
          <td>
            @if ($s->isDeletable())
            <delete-form route-action="{{ route('master.species.delete', $s->species_code) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $s->species_code }}</td>
          <td class="text-left">{{ $s->species_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
