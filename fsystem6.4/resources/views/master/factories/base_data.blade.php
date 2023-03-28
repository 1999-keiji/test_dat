@extends('master.factories.edit')

@section('nav-tabs')
  <li class="active"><a href="#">{{ __('view.master.global.base_data') }}</a></li>
  <li><a href="{{ route('master.factories.beds', $factory->factory_code) }}">{{ __('view.master.factories.layout') }}</a></li>
  <li><a href="{{ route('master.factories.warehouses', $factory->factory_code) }}">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li><a href="{{ route('master.factories.panels', $factory->factory_code) }}">{{ __('view.master.factories.panel') }}</a></li>
  <li><a href="{{ route('master.factories.cycle_patterns', $factory->factory_code) }}">{{ __('view.master.factories.pattern') }}</a></li>
  <li><a href="{{ route('master.factories.jccores', $factory->factory_code) }}">{{ __('view.master.factories.jccores') }}</a></li>
@endsection

@section('styles')
  @parent

  <style type="text/css">
    .form-horizontal label.checkbox-inline {
      padding-top: 0px;
    }
    .working-days {
      white-space: nowrap;
    }
  </style>
@endsection

@section('edit_content')
<form id="save-factory-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.factories.update', $factory->factory_code) }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      @canSave(Auth::user(), route_relatively('master.factories.index'))
      <button class="btn btn-default btn-lg save-button pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
      @endcanSave
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="factory_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.factory_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $factory->factory_code }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="factory_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.factory_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="factory_name" class="form-control  ime-active {{ has_error('factory_name') }}" maxlength="50" name="factory_name" type="text" value="{{ old('factory_name',$factory->factory_name) }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="factory_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.factory_abbreviation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="factory_abbreviation" class="form-control ime-active {{ has_error('factory_abbreviation') }}" maxlength="20" name="factory_abbreviation" type="text" value="{{ old('factory_abbreviation',$factory->factory_abbreviation) }}" required>
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
            value="{{ old('country_code',$factory->country_code) }}"
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
            type="text" value="{{ old('postal_code',$factory->postal_code) }}"
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
            <option value="{{ $value }}" {{ is_selected($value, old('prefecture_code',$factory->prefecture_code)) }}>{{ $value }}: {{ $label }}</option>
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
          <input id="address" class="form-control ime-active {{ has_error('address') }}" maxlength="50" name="address" type="text" value="{{ old('address',$factory->address) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.address2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address2" class="form-control ime-active {{ has_error('address2') }}" maxlength="50" name="address2" type="text" value="{{ old('address2',$factory->address2) }}">
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
          <input id="address3" class="form-control ime-active {{ has_error('address3') }}" maxlength="50" name="address3" type="text" value="{{ old('address3',$factory->address3) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address" class="form-control ime-inactive {{ has_error('abroad_address') }}" maxlength="50" name="abroad_address" type="text" value="{{ old('abroad_address',$factory->abroad_address) }}">
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
          <input id="abroad_address2" class="form-control ime-inactive {{ has_error('abroad_address2') }}" maxlength="50" name="abroad_address2" type="text" value="{{ old('abroad_address2',$factory->abroad_address2) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address3" class="form-control ime-inactive {{ has_error('abroad_address3') }}" maxlength="50" name="abroad_address3" type="text" value="{{ old('abroad_address3',$factory->abroad_address3) }}">
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
          <input id="phone_number" class="form-control ime-inactive {{ has_error('phone_number') }}" maxlength="20" name="phone_number" type="text" value="{{ old('phone_number',$factory->phone_number) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="extension_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.extension_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="extension_number" class="form-control ime-inactive {{ has_error('extension_number') }}" maxlength="15" name="extension_number" type="text" value="{{ old('extension_number',$factory->extension_number) }}">
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
          <input id="fax_number" class="form-control ime-inactive {{ has_error('fax_number') }}" maxlength="15" name="fax_number" type="text" value="{{ old('fax_number',$factory->fax_number) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.mail_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="mail_address" class="form-control ime-inactive {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address',$factory->mail_address) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.factories.working_day') }}
        </label>
        <div class="col-md-7 col-sm-7 working-days">
          @foreach ($working_date->getDayOfTheWeeks() as $value => $label)
          <label class="checkbox-inline">
            <input
              value="{{ $value % $working_date::DAYS_PER_WEEK }}"
              type="checkbox"
              name="working_days[]"
              @if (in_array($value % $working_date::DAYS_PER_WEEK, old('working_days', $factory->getWorkingDayOfTheWeeks())))
              checked
              @endif>{{ $label }}
          </label>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.corporations.corporation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="corporation_code" class="form-control {{ has_error('corporation_code') }}" name="corporation_code" required>
            @foreach ($corporations as $c)
            <option value="{{ $c->corporation_code }}" {{ is_selected($c->corporation_code, old('corporation_code',$factory->corporation_code)) }}>{{ $c->corporation_abbreviation }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.supplier_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="supplier_code"
            class="form-control ime-inactive {{ has_error('supplier_code') }}"
            maxlength="{{ $supplier_code->getMaxLength() }}"
            name="supplier_code"
            type="text"
            value="{{ old('supplier_code', $factory->supplier_code) }}"
            data-toggle="tooltip"
            title="{{ replace_el($supplier_code->getHelpText()) }}"
            required>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="symbolic_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.symbolic_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="symbolic_code"
            class="form-control ime-inactive {{ has_error('symbolic_code') }}"
            maxlength="{{ $symbolic_code->getMaxLength() }}"
            name="symbolic_code"
            type="text"
            value="{{ old('symbolic_code', $factory->symbolic_code) }}"
            data-toggle="tooltip"
            title="{{ replace_el($symbolic_code->getHelpText()) }}"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="global_gap_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.global_gap_number') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="global_gap_number" class="form-control ime-active {{ has_error('global_gap_number') }}" maxlength="15" name="global_gap_number" type="text" value="{{ old('global_gap_number', $factory->global_gap_number) }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="invoice_bank_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.bank_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="invoice_bank_name" class="form-control ime-active {{ has_error('invoice_bank_name') }}" maxlength="40" name="invoice_bank_name" type="text" value="{{ old('invoice_bank_name', $factory->invoice_bank_name) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="invoice_bank_branch_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.bank_branch_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="invoice_bank_branch_name" class="form-control ime-active {{ has_error('invoice_bank_branch_name') }}" maxlength="40" name="invoice_bank_branch_name" type="text" value="{{ old('invoice_bank_branch_name', $factory->invoice_bank_branch_name) }}" required>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="invoice_bank_account_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.bank_account_number') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="invoice_bank_account_number" class="form-control ime-inactive {{ has_error('invoice_bank_account_number') }}" maxlength="8" name="invoice_bank_account_number" type="text" value="{{ old('invoice_bank_account_number', $factory->invoice_bank_account_number) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="invoice_bank_account_holder" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.factories.bank_account_holder') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="invoice_bank_account_holder" class="form-control ime-active {{ has_error('invoice_bank_account_holder') }}" maxlength="40" name="invoice_bank_account_holder" type="text" value="{{ old('invoice_bank_account_holder', $factory->invoice_bank_account_holder) }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.factories.needs_to_slide_printing_shipping_date') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <label class="checkbox-inline">
            <input type="checkbox" name="needs_to_slide_printing_shipping_date" value="1" {{ is_checked(1, old('needs_to_slide_printing_shipping_date', $factory->needs_to_slide_printing_shipping_date)) }}>
            {{ __('view.master.factories.slide') }}
          </label>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.global.remark') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <textarea id="remark" class="form-control ime-active {{ has_error('remark') }}" name="remark" rows="3">{{ old('remark') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <overwrite-on-invoice
    :factory="{{ $factory }}"
    :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
    :errors="{{ $errors }}">
  </overwrite-on-invoice>

  <input name="updated_at" type="hidden" value="{{ $factory->updated_at->format('Y-m-d H:i:s') }}">
  {{ method_field('PATCH') }}
  {{ csrf_field() }}
  @canSave(Auth::user(), route_relatively('master.factories.index'))
  <input id="can-save-data" type="hidden" value="1">
  @else
  <input id="can-save-data" type="hidden" value="0">
  @endcanSave
</form>
@endsection
