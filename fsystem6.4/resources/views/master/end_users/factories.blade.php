@extends('layout')

@section('title')
{{ __('view.master.end_users.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.end_users.index') }}">{{ __('view.master.end_users.index') }}</a>
  </li>
  <li>{{ __('view.master.end_users.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.end_users.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<ul class="nav nav-tabs">
  <li>
    <a href="{{ route('master.end_users.edit', $end_user->getJoinedPrimaryKeys()) }}">{{ __('view.master.global.base_data') }}</a>
  </li>
  <li class="active">
    <a href="#">{{ __('view.master.factories.factory') }}</a>
  </li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="factory_tab">
    @canSave(Auth::user(), route_relatively('master.end_users.index'))
    <form id="save-end_user-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.end_users.factories.create') }}" method="POST">
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-6 col-sm-offset-6 col-xs-offset-6">
          @canSave(Auth::user(), route_relatively('master.end_users.index'))
          <button class="btn btn-default btn-lg pull-right save-data" type="button">
            <i class="fa fa-save"></i> {{ __('view.global.save') }}
          </button>
          @endcanSave
        </div>
      </div>
      <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-4 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label id="factory_code_label" for="factory_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.factories.factory') }}
            </label>
            <div class="col-md-7 col-sm-7 col-xs-9">
              <select id="factory_code" class="form-control {{ has_error('factory_code') }}" name="factory_code">
                @foreach ($factories as $f)
                <option value="{{ $f->factory_code }}" {{ is_selected($f->factory_code, old('factory_code', '')) }}>
                  {{ $f->factory_abbreviation }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
      <input name="end_user_code" type="hidden" value="{{ $end_user->end_user_code }}">
      {{ csrf_field() }}
      {{ method_field('POST') }}
    </form>
    @endcanSave

    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed">
          <thead>
            <tr>
              @canSave(Auth::user(), route_relatively('master.end_users.index'))
              <th>{{ __('view.global.delete') }}</th>
              @endcanSave
              <th>{{ __('view.master.factories.factory_code') }}</th>
              <th>{{ __('view.master.factories.factory_name') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($end_user->end_user_factories as $ef)
            <tr>
              @canSave(Auth::user(), route_relatively('master.end_users.index'))
              <td>
                <delete-form route-action="{{ route('master.end_users.factories.delete', $ef->getJoinedPrimaryKeys()) }}">
                </delete-form>
              </td>
              @endcanSave
              <td class="text-left">{{ $ef->factory_code }}</td>
              <td class="text-left">{{ $ef->factory->factory_abbreviation }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
