@extends('master.factories.edit')

@section('nav-tabs')
  <li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li><a href="{{ route('master.factories.beds', $factory->factory_code) }}">{{ __('view.master.factories.layout') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li><a href="{{ route('master.factories.panels', $factory->factory_code) }}">{{ __('view.master.factories.panel') }}</a></li>
  <li><a href="{{ route('master.factories.cycle_patterns', $factory->factory_code) }}">{{ __('view.master.factories.pattern') }}</a></li>
  <li><a href="{{ route('master.factories.jccores', $factory->factory_code) }}">{{ __('view.master.factories.jccores') }}</a></li>
@endsection

@section('edit_content')
<form id="add-factory-warehouse-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.factories.warehouses.create', $factory->factory_code) }}" method="POST">
  @canSave(Auth::user(), route_relatively('master.factories.index'))
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      @if ($not_linked_warehouses->isNotEmpty())
      <div class="row form-group">
        <label for="warehouse_code" class="col-md-4 col-sm-4 control-label">
          {{ __('view.master.warehouses.warehouse') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="warehouse_code" class="form-control {{ has_error('warehouse_code') }}" name="warehouse_code"
          @if ($not_linked_warehouses->isEmpty()) disabled @endif>
            @foreach ($not_linked_warehouses as $warehouse)
            <option value="{{ $warehouse->warehouse_code }}" {{ is_selected($warehouse->warehouse_code, old('warehouse_code')) }}>{{ $warehouse->warehouse_abbreviation }}</option>
            @endforeach
          </select>
        </div>
      </div>
      @endif
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      @if ($factory_warehouses->isNotEmpty())
      <button id="save-warehouse-priorities" class="btn btn-default btn-lg save-button pull-right" type="button">
        <i class="fa fa-save"></i>
        {{ __('view.master.factories.priority') }}{{ __('view.global.save') }}
      </button>
      @endif
      @if ($not_linked_warehouses->isNotEmpty())
      <button id="save-factory-warehouse" class="btn btn-default btn-lg save-button pull-right" type="button">
        <i class="fa fa-plus"></i>
        {{ __('view.master.warehouses.warehouse') }}{{ __('view.global.add') }}
      </button>
      @endif
    </div>
  </div>
  @endcanSave
  {{ method_field('POST') }}
  {{ csrf_field() }}
</form>

<form id="edit-factory-warehouse-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.factories.warehouses.update', $factory->factory_code) }}" method="POST">
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered table-more-condensed">
        <thead>
          <tr>
            @canSave(Auth::user(), route_relatively('master.factories.index'))
            <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.delete') }}</th>
            @endcanSave
            <th class="col-md-5 col-sm-5 col-xs-5">{{ __('view.master.warehouses.warehouse_name') }}</th>
            <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.master.factories.priority') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($factory_warehouses as $fw)
          <tr>
            @canSave(Auth::user(), route_relatively('master.factories.index'))
            <td>
              @if ($fw->isDeletable())
                <delete-form route-action="{{ route('master.factories.warehouses.delete', [$factory->factory_code, $fw->getJoinedPrimaryKeys()]) }}">
                </delete-form>
              @endif
            </td>
            @endcanSave
            <td class="text-left">{{ $fw->warehouse->warehouse_abbreviation }}</td>
            <td class="text-center">
              @canSave(Auth::user(), route_relatively('master.factories.index'))
              <select id="priority" class="form-control {{ has_error('priority') }}" name="priorities[{{ $fw->warehouse_code }}]" >
                @foreach ($factory_warehouses as $count)
                <option value="{{ $loop->iteration }}" {{ is_selected($loop->iteration, $fw->priority) }}>{{ $loop->iteration }}</option>
                @endforeach
              </select>
              @else
              {{ $fw->priority }}
              @endcanSave
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  {{ method_field('PATCH') }}
  {{ csrf_field() }}
</form>
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $(function () {
      $('#save-warehouse-priorities').click(function () {
        if (confirm('優先度を保存しますか?')) {
          $('.alert').remove()
          $(this).prop('disabled', true)
          $('#edit-factory-warehouse-form').submit()
        }
      })

      $('#save-factory-warehouse').click(function () {
        if (confirm('倉庫を追加しますか?')) {
          $('.alert').remove()
          $('.save-factory-warehouse').prop('disabled', true)
          $('#add-factory-warehouse-form').submit()
        }
      })
    })
  </script>
@endsection
