@extends('layout')

@section('title')
{{ __('view.plan.growth_simulation_fixed.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_simulation_fixed.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('plan.growth_simulation_fixed.search') }}" method="POST">
    <div class="col-md-8 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <search-growth-simulations-form
        :factories="{{ $factories }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :fixed-flag="{{ json_encode(true) }}">
      </search-growth-simulations-form>
    </div>
    <div class="col-md-1 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-2">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $growth_simulations->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-4 col-sm-4 col-md-offset-2 col-sm-offset-2">
    {{ $growth_simulations->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-10 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.reference') }}</th>
          <th>{{ __('view.global.harvest') }}</th>
          <th>@sortablelink('growth_simulation.factory_code', __('view.master.factories.factory'))</th>
          <th>@sortablelink('growth_simulation.factory_species_code', __('view.master.factory_species.factory_species'))</th>
          <th>@sortablelink('growth_simulation.simulation_name', __('view.plan.growth_simulation.simulation_name'))</th>
          <th>@sortablelink('growth_simulation.fixed_comp_at', __('view.global.fixed_at'))</th>
          <th>@sortablelink('growth_simulation.fixed_by', __('view.global.fixed_by'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($growth_simulations as $gs)
          <tr>
            <td>
              @if ($gs->isFixingSimulation())
              <a class="btn btn-sm btn-default active disabled">{{ __('view.global.fixing') }}</a>
              @else
              <a class="btn btn-sm btn-primary" href="{{ route('plan.growth_simulation.edit', $gs->getJoinedPrimaryKeys()) }}">
                {{ __('view.global.reference') }}
              </a>
              @endif
            </td>
            <td>
              @if ($gs->isFixingSimulation())
              <a class="btn btn-sm btn-default active disabled">{{ __('view.global.fixing') }}</a>
              @else
              <growth-simulation-details
                :growth-simulation="{{ $gs }}"
                :growth-simulation-item-lists="{{ $gs->growth_simulation_items->groupByInputGroup() }}"
                :growing-stage-list="{{ json_encode($growing_stage_list) }}">
              </growth-simulation-details>
              @endif
            </td>
            <td class="text-left">{{ $gs->factory_abbreviation }}</td>
            <td class="text-left">{{ $gs->factory_species_name }}</td>
            <td class="text-left">{{ $gs->simulation_name }}</td>
            <td>
              @if ($gs->isFixingSimulation())
              {{ __('view.global.fixing') }}（{{ $gs->fixed_start_at->format('Y/m/d H:i:s') }}）
              @else
              {{ $gs->fixed_comp_at->format('Y/m/d H:i:s') }}
              @endif
            </td>
            <td class="text-left">{{ $gs->fixed_name }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
