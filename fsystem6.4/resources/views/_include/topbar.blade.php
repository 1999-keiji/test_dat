<div class="top_nav">
  <div class=
    "nav_menu row
    @if (app()->environment() === 'staging')
    env-staging
    @endif
    ">
    <div class="col-md-2 col-sm-2 col-xs-2">
      <span id="system-logo"><span id="system-logo-header">F</span>System</span>
      @if (app()->environment() === 'staging')
      <span id="staging-mark">[検証]</span>
      @endif
    </div>
    @auth
    <div class="col-md-7 col-sm-7 col-xs-6">
      <ol id="breadcrumb"
        class=
          "breadcrumb
          @if (app()->environment() === 'staging')
          env-staging
          @endif">
        <li>
          <a class="clear-session" href="#">@yield('menu-section')</a>
        </li>
        @yield('breadcrumbs')
      </ol>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-4">
      <ul
        class=
          "nav navbar-nav navbar-right
          @if (app()->environment() === 'staging')
          env-staging
          @endif">
        <li>
          <a href="{{ route('auth.password.index') }}">
            <i class="fa fa-user"></i> {{ Auth::user()->user_name }}
          </a>
        </li>
        <li>
          <form action="{{ route('auth.logout') }}" method="POST">
            <button id="logout-btn" class="btn btn-default" type="submit">
              <i class="fa fa-sign-out"></i> {{ __('view.layout.logout') }}
            </button>
            {{ csrf_field() }}
            {{ method_field('POST') }}
          </form>
        </li>
      </ul>
    </div>
    @endauth
  </div>
  <form id="clear-session-form" action="{{ route('index.clear') }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>
