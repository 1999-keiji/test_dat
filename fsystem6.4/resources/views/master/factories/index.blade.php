@extends('layout')

@section('title')
{{ __('view.master.factories.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.factories.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.factories.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered search-factories-table">
        <tr>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.corporations.corporation') }}</th>
          <td class="col-md-3 col-sm-3 col-xs-3">
            <select id="corporation_code" class="form-control {{ has_error('corporation_code') }}" name="corporation_code">
              <option value=""></option>
              @foreach ($corporations as $c)
              <option value="{{ $c->corporation_code }}" {{ is_selected($c->corporation_code, old('corporation_code', $params['corporation_code'] ?? '')) }}>
                {{ $c->corporation_abbreviation }}
              </option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.factories.factory_code') }}</th>
          <td class="col-md-3 col-sm-3 col-xs-3">
            <input
              name="factory_code"
              class="form-control ime-inactive {{ has_error('factory_code') }}"
              maxlength="{{ $factory_code->getMaxLength() }}"
              value="{{ old('factory_code', $params['factory_code'] ?? '') }}"
              data-toggle="tooltip"
              title="{{ replace_el($factory_code->getHelpText()) }}">
          </td>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.factories.factory_name') }}</th>
          <td class="col-md-5 col-sm-5 col-xs-5">
            <input
              name="factory_name"
              class="form-control ime-active {{ has_error('factory_name') }}"
              maxlength="50"
              type="text"
              value="{{ old('factory_name', $params['factory_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.factories.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.factories.add') }}">
        <i class="fa fa-plus"></i> {{ __('view.global.add') }}
      </a>
      @endcanSave
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
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $factories->total() }}</span>
      {{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $factories->appends(request(['sort', 'order']))->links() }}
  </div>
</div>
<div class="row">
  <div class="col-md-9 col-sm-9 col-xs-10 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.factories.index'))
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.delete') }}</th>
          @endcanSave
          <th class="col-md-2 col-sm-2 col-xs-2">@sortablelink('corporation.corporation_name', __('view.master.corporations.corporation'))</th>
          <th class="col-md-2 col-sm-2 col-xs-2">@sortablelink('factory_code', __('view.master.factories.factory_code'))</th>
          <th class="col-md-6 col-sm-6 col-xs-6">@sortablelink('factory_name', __('view.master.factories.factory_name'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($factories as $f)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.factories.edit', $f->factory_code) }}">{{ __('view.global.edit') }}</a>
          </td>
          @canSave(Auth::user(), route_relatively('master.factories.index'))
          <td>
            <delete-form route-action="{{ route('master.factories.delete', $f->factory_code) }}">
            </delete-form>
          </td>
          @endcanSave
          <td class="text-left">{{ $f->corporation->corporation_abbreviation }}</td>
          <td class="text-left">{{ $f->factory_code }}</td>
          <td class="text-left">{{ $f->factory_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
