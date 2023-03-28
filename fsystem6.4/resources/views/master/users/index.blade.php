@extends('layout')

@section('title')
{{ __('view.master.users.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.users.index') }}">{{ __('view.master.users.index') }}</a>
  </li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.users.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.users.user_code') }}</th>
          <td>
            <input class="form-control ime-inactive" maxlength="{{ $user_code->getMaxLength() }}" name="user_code" value="{{ old('user_code', $params['user_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.users.user_name') }}</th>
          <td>
            <input class="form-control ime-active" maxlength="30" name="user_name" value="{{ old('user_name', $params['user_name'] ?? '') }}">
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.users.affiliation') }}</th>
          <td>
            <select id="affiliation" class="form-control" name="affiliation">
              <option value=""></option>
              @foreach ($affiliation->all() as $label => $value)
              <option
                value="{{ $value }}"
                data-can-select-factory="{{ $affiliation->belongsToFactory($value) ? 1 : 0 }}"
                {{ is_selected($value, $params['affiliation'] ?? '') }}>
                {{ $label }}
              </option>
              @endforeach
            </select>
          </td>
          <th>{{ __('view.master.factories.factory') }}</th>
          <td>
            <select
              id="factory_code"
              class="form-control"
              name="factory_code"
              @if (! $affiliation->belongsToFactory((int)($params['affiliation'] ?? '')))
              disabled
              @endif>
              <option value=""></option>
              @foreach ($factories as $value)
              <option value="{{$value->factory_code}}" {{ is_selected($value->factory_code, $params['factory_code'] ?? '') }}>{{ $value->factory_abbreviation }}</option>
              @endforeach
            </select>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.users.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.users.add') }}">
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
  <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $users->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $users->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-9 col-sm-9 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.users.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('user_code', __('view.master.users.user_code'))</th>
          <th>@sortablelink('user_name', __('view.master.users.user_name'))</th>
          <th>@sortablelink('affiliation', __('view.master.users.affiliation'))</th>
          <th>@sortablelink('factory', __('view.master.factories.factory'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $u)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.users.edit', $u->user_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.users.index'))
          <td>
            <delete-form route-action="{{ route('master.users.delete', $u->user_code) }}">
            </delete-form>
          </td>
          @endcanSave
          <td class="text-left">{{ $u->user_code }}</td>
          <td class="text-left">{{ $u->user_name }}</td>
          <td class="text-left">{{ $u->affiliation->label() }}</td>
          <td class="text-left">
            @if ($u->affiliation->belongsToFactory())
            {{ $u->getAffilicatedFactories()->pluck('factory_abbreviation')->implode(',') }}
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
