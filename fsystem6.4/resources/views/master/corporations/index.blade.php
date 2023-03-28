@extends('layout')

@section('title')
{{ __('view.master.corporations.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.corporations.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.corporations.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.corporations.corporation_code') }}</th>
          <td>
            <input class="form-control ime-inactive" maxlength="{{ $corporation_code->getMaxLength() }}" name="corporation_code" value="{{ old('corporation_code', $params['corporation_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.corporations.corporation_name') }}</th>
          <td>
            <input class="form-control ime-active" maxlength="50" name="corporation_name" value="{{ old('corporation_name', $params['corporation_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.corporations.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.corporations.add') }}">
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

@if (count($params))
<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $corporations->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $corporations->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.corporations.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('corporation_code', __('view.master.corporations.corporation_code'))</th>
          <th>@sortablelink('corporation_name', __('view.master.corporations.corporation_name'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($corporations as $c)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.corporations.edit', $c->corporation_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.corporations.add'))
          <td>
            @if ($c->isDeletable())
            <delete-form route-action="{{ route('master.corporations.delete', $c->corporation_code) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $c->corporation_code }}</td>
          <td class="text-left">{{ $c->corporation_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
