@extends('layout')

@section('title')
{{ __('view.factory_production_work.activity_results.index') }}
@endsection

@section('menu-section')
{{ __('view.index.factory-production-work') }}
@endsection

@section('breadcrumbs')
<li>
  <a class="clear-search-params" href="#">{{ __('view.factory_production_work.activity_results.index') }}</a>
</li>
<li>{{ __('view.factory_production_work.activity_results.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <form id="clear-search-params-form" action="{{ route('factory_production_work.activity_results.panel_search', $factory_species->getJoinedPrimaryKeys()) }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>

  <div class="col-md-8 col-sm-8 col-xs-5">
    <a class="btn btn-default btn-lg back-button clear-search-params" href="#">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  <form action="{{ route('factory_production_work.activity_results.panel_search', $factory_species->getJoinedPrimaryKeys()) }}" method="POST">
    <div class="col-md-7 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered table-layout-fixed">
        <tbody>
          <tr>
            <th>{{ __('view.master.factories.factory') }}</th>
            <td class="text-left"><label>{{ $factory_species->factory->factory_abbreviation }}</label></td>
            <th>{{ __('view.master.species.species') }}</th>
            <td class="text-left"><label>{{ $factory_species->species->species_name }}</label></td>
          </tr>
          <tr>
            <th>{{ __('view.master.factory_species.factory_species') }}</th>
            <td class="text-left"><label>{{ $factory_species->factory_species_name }}</label></td>
            <th>{{ __('view.factory_production_work.work_instruction.working_date') }}</th>
            <td>
              <label>{{ $params['working_date'] }}</label>
              <input type="hidden" name="working_date" value="{{ $params['working_date'] }}">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-7 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered table-layout-fixed">
        <tbody>
          <tr>
            <th>
              {{ __('view.master.factory_beds.bed') }}{{ __('view.master.factory_beds.row') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <select class="form-control" name="row">
                @foreach (range(1, $factory_species->factory->number_of_rows) as $row)
                <option value="{{ $row }}" {{ is_selected($row, $panel_params['row'] ?? null) }}>{{ $row }}</option>
                @endforeach
              </select>
            </td>
            <th>
              {{ __('view.master.factory_beds.bed') }}{{ __('view.master.factory_beds.column') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <select class="form-control" name="column">
                @foreach (range(1, $factory_species->factory->number_of_columns) as $column)
                <option value="{{ $column }}" {{ is_selected($column, $panel_params['column'] ?? null) }}>{{ $column }}</option>
                @endforeach
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-2 col-md-pull-1 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-2">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($panel_params) !== 0)
<div class="row scroll-table-row">
  <div class="col-md-8 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1 separate-table-row list-head">
    <table class="table table-color-bordered table-layout-fixed set-width-target">
      <thead>
        <tr>
          <th>{{ __('view.factory_production_work.activity_results.panel_position') }}</th>
          <th>{{ __('view.factory_production_work.activity_results.current_growing_stage') }}</th>
          <th>{{ __('view.factory_production_work.activity_results.number_of_holes') }}</th>
          <th>{{ __('view.factory_production_work.activity_results.panel_status') }}</th>
          <th>{{ __('view.factory_production_work.activity_results.using_hole_count') }}</th>
        </tr>
      </thead>
    </table>
  </div>
  <form class="save-data-form" action="{{ route('factory_production_work.activity_results.update', $factory_species->getJoinedPrimaryKeys()) }}" method="POST">
    <div class="col-md-8 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1 list-body">
      <table class="table table-color-bordered table-layout-fixed get-width-target">
        <tbody>
          @foreach ($panels as $p)
          <tr>
            <td>{{ $p->y_current_bed_position }} × {{ $p->x_current_bed_position }}</td>
            <td class="text-left">{{ $p->growing_stage_name }}</td>
            <td class="text-right">{{ $p->number_of_holes }}</td>
            <td>
              <select class="form-control panel_status" name="panel_status[]" required>
                @foreach ($panel_status->all() as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, $p->panel_status) }}>{{ $label }}</option>
                @endforeach
              </select>
            </td>
            <td>
              @if ($p->panel_status === $panel_status::OPERATION)
              <input class="form-control text-right using_hole_count" type="number" name="using_hole_count[]" value="{{ $p->using_hole_count }}" required>
              @else
              <input class="form-control text-right using_hole_count" type="number" name="using_hole_count[]" value="{{ $p->using_hole_count ?: '' }}" readonly required>
              @endif
            </td>
          </tr>
          <input type="hidden" name="panel_id[]" value="{{ $p->panel_id }}">
          @endforeach
        </tbody>
      </table>
    </div>
    @if (count($panels) !== 0)
    <div class="col-md-1 col-md-pull-1 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-2">
      @canSave(Auth::user(), route_relatively('factory_production_work.activity_results.index'))
      <button class="btn btn-lg btn-default pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
      @endcanSave
    </div>
    @endif
    <input type="hidden" name="row" value="{{ $panel_params['row'] }}">
    <input type="hidden" name="column" value="{{ $panel_params['column'] }}">

    @canSave(Auth::user(), route_relatively('factory_production_work.activity_results.index'))
    <input id="can-save-data" type="hidden" value="1">
    @else
    <input id="can-save-data" type="hidden" value="0">
    @endcanSave
    {{ csrf_field() }}
    {{ method_field('PATCH') }}
  </form>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('.clear-search-params').click(function () {
      $('#clear-search-params-form').submit()
    })

    $('select.panel_status').change(function () {
      var $target = $(this).parent().next().children('.using_hole_count')
      if ($(this).val() == {{ $panel_status::EMPTY }}) {
        $target.val('').prop('readonly', true)
      } else {
        $target.removeAttr('readonly')
      }
    })
  </script>
@endsection
