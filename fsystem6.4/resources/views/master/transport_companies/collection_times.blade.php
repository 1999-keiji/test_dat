@extends('layout')

@section('title')
{{ __('view.master.transport_companies.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.transport_companies.index') }}">{{ __('view.master.transport_companies.index') }}</a>
  </li>
  <li>{{ __('view.master.transport_companies.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.transport_companies.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<ul class="nav nav-tabs">
  <li><a href="{{ route('master.transport_companies.edit', $transport_company->transport_company_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li class="active"><a href="#">{{ __('view.master.transport_companies.collection_time') }}</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active">
    <div class="row">
      <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-5 col-sm-offset-5 col-xs-offset-6">
        @canSave(Auth::user(), route_relatively('master.transport_companies.index'))
        <button type="button" class="btn btn-lg btn-default pull-right" data-toggle="modal" data-target="#add-collection-time-modal">
          <i class="fa fa-plus"></i> {{ __('view.global.add') }}
        </button>
        @endcanSave
      </div>
    </div>
    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
        <p>
          <span class="search-result-count">{{ $transport_company->collection_times->count() }}</span>
          {{ __('view.global.suffix_serach_result_count') }}
        </p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-sm-6 col-xs-10 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed">
          <thead>
            <tr>
              @canSave(Auth::user(), route_relatively('master.transport_companies.index'))
              <th>{{ __('view.global.edit') }}</th>
              <th>{{ __('view.global.delete') }}</th>
              @endcanSave
              <th>{{ __('view.master.transport_companies.collection_time') }}</th>
              <th>{{ __('view.global.remark') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($transport_company->collection_times as $ct)
            <tr>
              @canSave(Auth::user(), route_relatively('master.transport_companies.index'))
              <td>
                <button
                  type="button"
                  class="btn btn-sm btn-info edit-collection-time-button"
                  data-toggle="modal"
                  data-target="#edit-collection-time-modal"
                  data-primary-key="{{ $ct->getJoinedPrimaryKeys() }}"
                  data-collection-time="{{ $ct->collection_time }}"
                  data-remark="{{ $ct->remark }}">
                  {{ __('view.global.edit') }}
                </button>
              </td>
              <td>
                <delete-form route-action="{{ route('master.collection_times.delete', [$transport_company->transport_company_code, $ct->getJoinedPrimaryKeys()]) }}">
                </delete-form>
              </td>
              @endcanSave
              <td class="text-left">{{ $ct->collection_time }}</td>
              <td class="text-left">{{ $ct->remark }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div id="add-collection-time-modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
            <h4 class="modal-title">{{ __('view.master.collection_time.add') }}</h4>
          </div>
          <form class="form-horizontal basic-form save-data-form" action="{{ route('master.collection_times.create', $transport_company->transport_company_code) }}" method="POST">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="row form-group">
                    <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">
                      {{ __('view.master.collection_time.index') }}
                      <span class="required-mark">*</span>
                    </label>
                    <div class="col-md-5 col-sm-7">
                      <input id="collection_time" class="form-control ime-active {{ has_error('collection_time') }}" maxlength="50" name="collection_time" type="text" value="{{ old('collection_time') }}" required>
                    </div>
                  </div>
                  <div class="row form-group">
                    <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">
                      {{ __('view.global.remark') }}
                    </label>
                    <div class="col-md-5 col-sm-7">
                      <input id="remark" class="form-control ime-active {{ has_error('remark') }}" maxlength="50" name="remark" type="text" value="{{ old('remark') }}" required>
                    </div>
                  </div>
                </div>
              </div>
              {{ csrf_field() }}
              {{ method_field('POST') }}
            </div>
            <div class="modal-footer">
              <button class="btn btn-default btn-lg save-data" type="button">
                <i class="fa fa-save"></i> {{ __('view.global.save') }}
              </button>
              <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">{{ __('view.global.cancel') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div id="edit-collection-time-modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span>×</span>
            </button>
            <h4 class="modal-title">{{ __('view.master.collection_time.edit') }}</h4>
          </div>
          <form id="save-modal-form-edit" class="form-horizontal basic-form save-data-form" action="{{ route('master.collection_times.update', [$transport_company->transport_company_code, '']) }}" method="POST">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="row form-group">
                    <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">
                      {{ __('view.master.collection_time.index') }}
                      <span class="required-mark">*</span>
                    </label>
                    <div class="col-md-5 col-sm-7">
                      <input id="collection_time" class="form-control ime-active {{ has_error('collection_time') }}" maxlength="50" name="collection_time" type="text" value="{{ old('collection_time') }}" required>
                    </div>
                  </div>
                  <div class="row form-group">
                    <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">
                      {{ __('view.global.remark') }}
                    </label>
                    <div class="col-md-5 col-sm-7">
                      <input id="remark" class="form-control ime-active {{ has_error('remark') }}" maxlength="50" name="remark" type="text" value="{{ old('remark') }}" required>
                    </div>
                  </div>
                </div>
              </div>
              {{ method_field('PATCH') }}
              {{ csrf_field() }}
            </div>
            <div class="modal-footer">
              <button id="update-collection-time-button" class="btn btn-default btn-lg" type="button">
                <i class="fa fa-save"></i> {{ __('view.global.save') }}
              </button>
              <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">{{ __('view.global.cancel') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    var collectionTimePrimaryKey
    $('.edit-collection-time-button').click(function () {
      collectionTimePrimaryKey = $(this).data('primary-key')
      var $form = $('#save-modal-form-edit')
      $form.find('#collection_time').val($(this).data('collection-time'))
      $form.find('#remark').val($(this).data('remark'))
    })

    $('#update-collection-time-button').click(function () {
      if (confirm('データを登録しますか?')) {
        $('.alert').remove()
        $(this).prop('disabled', true)

        var $form = $('#save-modal-form-edit')
        $form.attr('action', $form.attr('action') + '/' + collectionTimePrimaryKey)

        $form.submit()
      }
    })
  </script>
@endsection
