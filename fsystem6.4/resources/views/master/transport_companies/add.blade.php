@extends('layout')

@section('title')
{{ __('view.master.transport_companies.add') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    {{ __('view.master.transport_companies.add') }}
  </li>
@endsection

@section('content')
<form id="save-transport-company-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.transport_companies.create') }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.transport_companies.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <button class="btn btn-default btn-lg pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="transport_company_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.transport_companies.transport_company_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="transport_company_code"
            class="form-control ime-active {{ has_error('transport_company_code') }}"
            maxlength="50"
            name="transport_company_code"
            type="text"
            value="{{ old('transport_company_code') }}"
            data-toggle="tooltip"
            title="{{ replace_el($transport_company_code->getHelpText()) }}"
            required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="transport_company_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.transport_companies.transport_company_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="transport_company_name" class="form-control ime-active {{ has_error('transport_company_name') }}" maxlength="50" name="transport_company_name" type="text" value="{{ old('transport_company_name') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="transport_branch_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.transport_companies.transport_branch_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="transport_branch_name" class="form-control ime-active {{ has_error('transport_branch_name') }}" maxlength="50" name="transport_branch_name" type="text" value="{{ old('transport_branch_name') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="transport_company_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.transport_companies.transport_company_abbreviation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="transport_company_abbreviation" class="form-control ime-active {{ has_error('transport_company_abbreviation') }}" maxlength="50" name="transport_company_abbreviation" type="text" value="{{ old('transport_company_abbreviation') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="country_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.country_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="country_code"
            class="form-control text-center ime-inactive {{ has_error('country_code') }}"
            maxlength="{{ $country_code->getMaxLength() }}"
            name="country_code"
            type="text"
            value="{{ old('country_code') }}"
            data-toggle="tooltip"
            title="{{ replace_el($country_code->getHelpText()) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="postal_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.postal_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="postal_code"
            class="form-control ime-inactive {{ has_error('postal_code') }}"
            maxlength="{{ $postal_code->getMaxLength() }}"
            name="postal_code"
            type="text" value="{{ old('postal_code') }}"
            data-toggle="tooltip"
            title="{{ replace_el($postal_code->getHelpText()) }}"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="prefecture_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.prefecture_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="prefecture_code" class="form-control {{ has_error('prefecture_code') }}" name="prefecture_code" data-toggle="tooltip" title="{{ replace_el($prefecture_code->getHelpText()) }}">
            <option value=""></option>
            @foreach ($prefecture_code->all() as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('prefecture_code')) }}>{{ $value }}: {{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.address') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address" class="form-control ime-active {{ has_error('address') }}" maxlength="50" name="address" type="text" value="{{ old('address') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.address2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address2" class="form-control ime-active {{ has_error('address2') }}" maxlength="50" name="address2" type="text" value="{{ old('address2') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address3" class="form-control ime-active {{ has_error('address3') }}" maxlength="50" name="address3" type="text" value="{{ old('address3') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address" class="form-control ime-inactive {{ has_error('abroad_address') }}" maxlength="50" name="abroad_address" type="text" value="{{ old('abroad_address') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="abroad_address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address2" class="form-control ime-inactive {{ has_error('abroad_address2') }}" maxlength="50" name="abroad_address2" type="text" value="{{ old('abroad_address2') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address3" class="form-control ime-inactive {{ has_error('abroad_address3') }}" maxlength="50" name="abroad_address3" type="text" value="{{ old('abroad_address3') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="phone_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.phone_number') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="phone_number" class="form-control ime-inactive {{ has_error('phone_number') }}" maxlength="20" name="phone_number" type="text" value="{{ old('phone_number') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="extension_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.extension_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="extension_number" class="form-control ime-inactive {{ has_error('extension_number') }}" maxlength="15" name="extension_number" type="text" value="{{ old('extension_number') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="fax_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.fax_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="fax_number" class="form-control ime-inactive {{ has_error('fax_number') }}" maxlength="15" name="fax_number" type="text" value="{{ old('fax_number') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.mail_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="mail_address" class="form-control ime-inactive {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="note" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.note') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <textarea id="note" class="form-control ime-active {{ has_error('note') }}" maxlength="255" name="note" rows="3">{{ old('note') }}</textarea>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="can_transport_double" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.transport_companies.can_transport_double') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="can_transport_double" name="can_transport_double" type="checkbox" required="required" value="1" {{ is_checked(1, old('can_transport_double', 0)) }}>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <div class="row form-group">
      <label for="remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
        {{ __('view.global.remark') }}
      </label>
      <div class="col-md-7 col-sm-7">
        <textarea id="remark" class="form-control ime-active {{ has_error('remark') }}" maxlength="255" name="remark" rows="3">{{ old('remark') }}</textarea>
      </div>
    </div>
  </div>
  </div>

{{ csrf_field() }}
{{ method_field('POST') }}
</form>
@endsection
