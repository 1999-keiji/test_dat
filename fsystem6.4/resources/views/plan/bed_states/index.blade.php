@extends('layout')

@section('title')
{{ __('view.plan.bed_states.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.bed_states.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form id="search-bed-states-form" action="{{ route('plan.bed_states.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-1 col-sm-offset-1">
      <search-bed-states-form
        :factories="{{ $factories }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}">
      </search-bed-states-form>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
  <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <add-bed-state-form
      route-action="{{ route('plan.bed_states.create') }}"
      :factories="{{ $factories }}">
    </add-bed-state-form>
    <button id="search-bed-states" class="btn btn-lg btn-default pull-right" type="button">
      <i class="fa fa-search"></i> {{ __('view.global.search') }}
    </button>
  </div>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $bed_states->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-4 col-sm-4 col-md-offset-2 col-sm-offset-2">
    {{ $bed_states->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-10 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.reference') }}</th>
          <th>{{ __('view.global.delete') }}</th>
          <th>@sortablelink('bed_states.factory_code', __('view.master.factories.factory'))</th>
          <th>@sortablelink('bed_states.factory_species_code', __('view.master.factory_species.factory_species'))</th>
          <th>@sortablelink('bed_states.start_of_week', __('view.global.date'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bed_states as $bs)
          <tr>
            <td>
              @if (! $bs->hasPrepared())
              <a class="btn btn-sm btn-default active disabled">{{ __('view.global.adding') }}</a>
              @else
              <a class="btn btn-sm btn-primary" href="{{ route('plan.bed_states.cultivation_states.index', $bs->getJoinedPrimaryKeys()) }}">
                {{ __('view.global.reference') }}
              </a>
              @endif
            </td>
            <td>
              @if (! $bs->hasPrepared())
              <a class="btn btn-sm btn-default active disabled">{{ __('view.global.adding') }}</a>
              @else
              <delete-form
                route-action="{{ route('plan.bed_states.delete', $bs->getJoinedPrimaryKeys()) }}">
              </delete-form>
              @endif
            </td>
            <td class="text-left">{{ $bs->factory_abbreviation }}</td>
            <td class="text-left">{{ $bs->factory_species_name }}</td>
            <td>{{ $bs->getStartOfWeek() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $(function () {
      $('#search-bed-states').click(function () {
        $('#search-bed-states-form').submit()
      })
    })
  </script>
@endsection
