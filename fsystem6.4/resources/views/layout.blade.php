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
      @include('_include.topbar')
      <div id="main-content" role="main">
        @include('_include.alert')
        @yield('content')
      </div>
    </div>
  </div>
  @section('scripts')
    <script src="{{ asset(mix('/js/app.js')) }}"></script>
  @show
</body>
</html>
