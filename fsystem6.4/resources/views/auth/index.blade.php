@extends('layout')

@section('title')
  {{ __('view.auth.index') }}
@endsection

@section('content')
<div class="row login-form">
  <div class="col-md-4 col-sm-4 col-xs-6 col-md-offset-4 col-sm-offset-4 col-xs-offset-3">
    <form class="form-horizontal basic-form" action="{{ route('auth.login') }}" method="POST">
      <div class="panel panel-success">
        <div class="panel-heading text-center">
          <h3 class="panel-title">{{ __('view.auth.index') }}</h3>
        </div>
        <div class="panel-body">
          <div class="row form-group">
            <label for="user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.users.user_code') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="user_code" class="form-control" name="user_code" type="text" autofocus required>
            </div>
          </div>
          <div class="row form-group">
            <label for="password" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.users.password') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="password" class="form-control" name="password" type="password" required>
            </div>
          </div>
          <button class="btn btn-default btn-lg btn-block" type="submit">
            <i class="fa fa-sign-in"></i> {{ __('view.auth.login') }}
          </button>
        </div>
      </div>
      {{ csrf_field() }}
      {{ method_field('POST') }}
    </form>
  </div>
</div>
@endsection
