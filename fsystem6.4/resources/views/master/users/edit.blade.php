@extends('layout')

@section('title')
{{ __('view.master.users.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.users.index') }}">{{ __('view.master.users.index') }}</a>
  </li>
  <li>{{ __('view.master.users.edit') }}</li>
@endsection

@section('content')
<form id="save-user-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.users.update', $user->user_code) }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.users.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      @canSave(Auth::user(), route_relatively('master.users.index'))
      <button class="btn btn-default btn-lg pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
      @endcanSave
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.users.user_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $user->user_code }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="user_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.users.user_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="user_name" class="form-control ime-active {{ has_error('user_name') }}" maxlength="30" name="user_name" type="text" value="{{ old('user_name', $user->user_name) }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="affiliation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.users.affiliation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="affiliation_user_edit" class="form-control {{ has_error('affiliation') }}" name="affiliation" data-action="{{ route('master.users.permissions') }}">
            @foreach ($affiliation->all() as $label => $value)
            <option value="{{$value}}"{{ is_selected($value, old('affiliation', $user->affiliation->value())) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="factory_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.factories.factory') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select-multiple
            id="factory_code"
            attr-name="factory_code[]"
            :options="{{ $factories->toJsonOptions() }}"
            :selected="{{ json_encode(old('factory_code', $user->affiliation->belongsToFactory() ? $user->getAffilicatedFactories()->pluck('factory_code')->all() : [])) }}"
            :is-disabled="{{ $affiliation->belongsToFactory((int)old('affiliation', $user->affiliation->value())) ? 0 : 1 }}">
          </select-multiple>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.mail_address') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="mail_address" class="form-control {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address', $user->mail_address) }}">
        </div>
      </div>
    </div>
  </div>

  @canSave(Auth::user(), route_relatively('master.users.index'))
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.users.password') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <button id="reset-password"  class="btn btn-default pull-left" type="button" data-action="{{ route('master.users.reset', $user->user_code) }}">
            {{ __('view.master.users.reset') }}
          </button>
        </div>
      </div>
    </div>
  </div>
  @endcanSave

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="permission" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.users.permission') }}
          <span class="required-mark">*</span>
        </label>
      </div>
      <div class="row form-group user-permission-row">
        <div class="col-md-offset-1 col-sm-offset-1 up-list-head">
          <table class="table table-color-bordered table-more-condensed set-width-target">
            <thead>
              <tr>
                <th>{{ __('view.master.users.category') }}</th>
                @foreach ($permission->all() as $label => $value)
                <th>{{ $label }}</th>
                @endforeach
              </tr>
            </thead>
          </table>
        </div>
        <div class="col-md-offset-1 col-sm-offset-1 up-list-body">
          <table class="table table-color-bordered table-more-condensed get-width-target">
            <tbody>
              @foreach ($permissions as $p)
              <tr>
                <td class="text-left">{{ implode(' ', [$p->group_name, $p->category_name]) }}</td>
                @foreach ($permission->all() as $label => $value)
                <td>
                  <input
                    type="radio"
                    name="permissions[{{ $p->category }}]"
                    value="{{ $value }}"
                    @if ($p->permission === $value)
                    checked
                    @endif>
                </td>
                @endforeach
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <input name="updated_at" type="hidden" value="{{ $user->updated_at->format('Y-m-d H:i:s') }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}

  @canSave(Auth::user(), route_relatively('master.users.index'))
  <input id="can-save-data" type="hidden" value="1">
  @else
  <input id="can-save-data" type="hidden" value="0">
  @endcanSave
</form>
@endsection
