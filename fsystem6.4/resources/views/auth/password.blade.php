@extends('layout')

@section('title')
{{ __('view.auth.change-password') }}
@endsection

@section('menu-section')
{{ __('view.index.main-menu') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.auth.change-password') }}</li>
@endsection

@section('content')
<form id="save-customer-form" class="form-horizontal basic-form save-data-form" action="{{ route('auth.password.change') }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" onclick="window.history.back();">
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
        <label for="user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.users.user_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $user->user_code }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="user_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.users.user_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $user->user_name }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="current_password" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.auth.current_password') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="current_password" class="form-control {{ has_error('current_password') }}" maxlength="50" name="current_password" type="password" value="" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="new_password" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.auth.password') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="password" class="form-control {{ has_error('password') }}" maxlength="50" name="password" type="password" value="" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="password_confirmation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.auth.password_confirmation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="password_confirmation" class="form-control {{ has_error('password_confirmation') }}" maxlength="50" name="password_confirmation" type="password" value="" required>
        </div>
      </div>
    </div>
  </div>
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
</form>
@endsection
