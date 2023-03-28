@extends('layout')

@section('title')
{{ __('view.master.users.add') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.users.index') }}">{{ __('view.master.users.index') }}</a>
  </li>
  <li>{{ __('view.master.users.add') }}</li>
@endsection

@section('content')
<form id="save-user-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.users.create') }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.users.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <button class="btn btn-default btn-lg pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.users.user_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="user_code"
            class="form-control ime-inactive {{ has_error('user_code') }}"
            maxlength="{{ $user_code->getMaxLength() }}"
            name="user_code"
            type="text"
            value="{{ old('user_code') }}"
            data-toggle="tooltip"
            title="{{ replace_el($user_code->getHelpText()) }}"
            required>
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
          <input id="user_name" class="form-control ime-active {{ has_error('user_name') }}" maxlength="30" name="user_name" type="text" value="{{ old('user_name') }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="affiliation_user_add" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.users.affiliation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="affiliation_user_add" class="form-control {{ has_error('affiliation') }}" name="affiliation" data-action="{{ route('master.users.permissions') }}">
            <option value=""></option>
            @foreach ($affiliation->all() as $label => $value)
            <option value="{{$value}}" {{ is_selected($value, old('affiliation')) }}>
              {{ $label }}
            </option>
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
            :selected="{{ json_encode(old('factory_code', [])) }}"
            :is-disabled="{{ $affiliation->belongsToFactory((int)old('affiliation')) ? 0 : 1 }}">
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
          <input id="mail_address" class="form-control {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="permission" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.users.permission') }}
          <span class="required-mark">*</span>
        </label>
      </div>
      <div class="row user-permission-row">
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
  {{ csrf_field() }}
  {{ method_field('POST') }}
</form>
@endsection
