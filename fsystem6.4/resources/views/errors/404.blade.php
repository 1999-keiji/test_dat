<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }} | @yield('title')</title>
  @section('styles')
    <link rel="stylesheet" href="{{ asset(mix('/css/app.css')) }}">
  @show
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="nav-md">
  <div id="app" class="container body">
    <div class="main_container">
      <div class="top_nav">
        <div class="nav_menu row">
          <div class="col-md-2 col-sm-2 col-xs-2">
            <span id="system-logo"><span id="system-logo-header">F</span>System</span>
          </div>
          <div class="col-md-7 col-sm-7 col-xs-6">
            <ol id="breadcrumb" class="breadcrumb">
              <li>
                <a class="clear-session" href="#">
                  {{ __('view.index.main-menu') }}
                </a>
              </li>
              @yield('breadcrumbs')
            </ol>
          </div>
        </div>
        <form id="clear-session-form" action="{{ route('index.clear') }}" method="POST">
          {{ csrf_field() }}
          {{ method_field('POST') }}
        </form>
      </div>
      <div id="main-content" role="main">
        <h2>存在しないページです。</h2>
      </div>
    </div>
  </div>
  @section('scripts')
    <script src="{{ asset(mix('/js/app.js')) }}"></script>
  @show
</body>
</html>
