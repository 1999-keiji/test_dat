@extends('layout')

@section('menu-section')
{{ __('view.index.main-menu') }}
@endsection

@section('content')
<ul class="nav nav-tabs">
  @foreach ($tabs as $t)
  <li @if ($t->tab_code === $active_tab) class="active" @endif>
    <a href="#{{ $t->tab_code }}" data-toggle="tab">{{ $t->tab_name }}</a>
  </li>
  @endforeach
</ul>

<div class="tab-content">
  @foreach ($tabs as $t)
  <div id="{{ $t->tab_code }}" class="tab-pane
    @if ($t->tab_code === $active_tab)
    active
    @endif">
    <div class="row inner-tab">
      @foreach ($t->groups as $groups_per_row)
      <div class="col-md-3 col-sm-3 col-xs-3">
        @foreach ($groups_per_row as $group)
        <div id="main-menu-panel" class="panel panel-menu">
          <div class="panel-heading">
            <h5>{{ $group->group_name }}</h5>
          </div>
          <div class="panel-body">
            <ul class="nav nav-pills nav-stacked">
              @foreach ($group->categories as $category)
              <li>
                <a class="btn btn-default link-btn
                  @if (! Auth::user()->canAccess(route_relatively($t->tab_code.'.'.$category->category.'.index')))
                  disabled
                  @endif" href="{{ route($t->tab_code.'.'.$category->category.'.index') }}">{{ $category->category_name }}</a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
        @endforeach
      </div>
      @endforeach
    </div>
  </div>
  @endforeach
</div>
@endsection
