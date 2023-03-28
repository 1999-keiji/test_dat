@extends('layout')

@section('title')
{{ __('view.master.products.add') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.products.index') }}">{{ __('view.master.products.index') }}</a>
  </li>
  <li>{{ __('view.master.products.add') }}</li>
@endsection

@section('content')
<form id="save-product-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.products.create') }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.products.index') }}">
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
        <label for="product_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.product_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="product_code"
                 class="form-control {{ has_error('product_code') }}"
                 maxlength="{{ $product_code->getMaxLength() }}"
                 name="product_code"
                 type="text"
                 value="{{ old('product_code') }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($product_code->getHelpText()) }}"
                 required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.creating_type') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $creating_type->label() }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="species_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.species.species') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="species_code" class="form-control {{ has_error('species_code') }}" name="species_code" required>
            <option value=""></option>
            @foreach ($species as $s)
            <option value="{{ $s->species_code }}" {{ is_selected($s->species_code, old('species_code', '')) }}>{{ $s->species_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="product_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.product_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="product_name" class="form-control {{ has_error('product_name') }}" maxlength="40" name="product_name" type="text" value="{{ old('product_name') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="result_addup_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.result_addup_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="result_addup_code" class="form-control {{ has_error('result_addup_code') }}" maxlength="10" name="result_addup_code" type="text" value="{{ old('result_addup_code') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="result_addup_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.result_addup_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="result_addup_name" class="form-control {{ has_error('result_addup_name') }}" maxlength="30" name="result_addup_name" type="text" value="{{ old('result_addup_name') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="result_addup_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.result_addup_abbreviation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="result_addup_abbreviation" class="form-control {{ has_error('result_addup_abbreviation') }}" maxlength="10" name="result_addup_abbreviation" type="text" value="{{ old('result_addup_abbreviation') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="product_large_category" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.product_large_category') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="product_large_category"
                 class="form-control {{ has_error('product_large_category') }}"
                 maxlength="{{ $category_code->getMaxLength() }}"
                 name="product_large_category"
                 type="text"
                 value="{{ old('product_large_category') }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($category_code->getHelpText()) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="product_middle_category" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.product_middle_category') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="product_middle_category"
                 class="form-control {{ has_error('product_middle_category') }}"
                 maxlength="{{ $category_code->getMaxLength() }}"
                 name="product_middle_category"
                 type="text"
                 value="{{ old('product_middle_category') }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($category_code->getHelpText()) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="product_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.product_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="product_class" class="form-control {{ has_error('product_class') }}" name="product_class" required>
            <option value=""></option>
            @foreach ($product_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('product_class', '')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="custom_product_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.custom_product_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="custom_product_flag" name="custom_product_flag" type="checkbox" value="1" {{ is_checked(1, old('custom_product_flag', 0)) }}>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="sales_order_unit" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.sales_order_unit') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="sales_order_unit" class="form-control {{ has_error('sales_order_unit') }}" maxlength="3" name="sales_order_unit" type="text" value="{{ old('sales_order_unit') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="sales_order_unit_quantity" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.sales_order_unit_quantity') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $sales_order_unit_quantity->getMaxLength() }}"
            attr-name="sales_order_unit_quantity"
            value="{{ old('sales_order_unit_quantity') }}"
            decimals="{{ $sales_order_unit_quantity->getDecimals() }}"
            :has-error="{{ json_encode(has_error('sales_order_unit_quantity') !== '') }}"
            help-text="{{ replace_el($sales_order_unit_quantity->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="minimum_sales_order_unit_quantity" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.minimum_sales_order_unit_quantity') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $sales_order_unit_quantity->getMaxLength() }}"
            attr-name="minimum_sales_order_unit_quantity"
            value="{{ old('minimum_sales_order_unit_quantity') }}"
            decimals="{{ $sales_order_unit_quantity->getDecimals() }}"
            :has-error="{{ json_encode(has_error('minimum_sales_order_unit_quantity') !== '') }}"
            help-text="{{ replace_el($sales_order_unit_quantity->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="statement_of_delivery_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.statement_of_delivery_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="statement_of_delivery_name" class="form-control {{ has_error('statement_of_delivery_name') }}" maxlength="50" name="statement_of_delivery_name" type="text" value="{{ old('statement_of_delivery_name') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="pickup_slip_message" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.pickup_slip_message') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="pickup_slip_message" class="form-control {{ has_error('pickup_slip_message') }}" maxlength="40" name="pickup_slip_message" type="text" value="{{ old('pickup_slip_message') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="lot_target_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.lot_target_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="lot_target_flag" name="lot_target_flag" type="checkbox" value="1" {{ is_checked(1, old('lot_target_flag', 0)) }}>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="species_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.species_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="species_name" class="form-control {{ has_error('species_name') }}" maxlength="40" name="species_name" type="text" value="{{ old('species_name') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="export_target_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.export_target_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="export_target_flag" name="export_target_flag" type="checkbox" value="1" {{ is_checked(1, old('export_target_flag', 0)) }}>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="net_weight" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.net_weight') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $product_weight->getMaxLength() }}"
            attr-name="net_weight"
            value="{{ old('net_weight') }}"
            decimals="{{ $product_weight->getDecimals() }}"
            :has-error="{{ json_encode(has_error('net_weight') !== '') }}"
            help-text="{{ replace_el($product_weight->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="gross_weight" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.gross_weight') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $product_weight->getMaxLength() }}"
            attr-name="gross_weight"
            value="{{ old('gross_weight') }}"
            decimals="{{ $product_weight->getDecimals() }}"
            :has-error="{{ json_encode(has_error('gross_weight') !== '') }}"
            help-text="{{ replace_el($product_weight->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="depth" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.depth') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $product_size->getMaxLength() }}"
            attr-name="depth"
            value="{{ old('depth') }}"
            decimals="{{ $product_size->getDecimals() }}"
            :has-error="{{ json_encode(has_error('depth') !== '') }}"
            help-text="{{ replace_el($product_size->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="width" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.width') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $product_size->getMaxLength() }}"
            attr-name="width"
            value="{{ old('width') }}"
            decimals="{{ $product_size->getDecimals() }}"
            :has-error="{{ json_encode(has_error('width') !== '') }}"
            help-text="{{ replace_el($product_size->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="height" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.height') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input-number-with-formatter
            max-length="{{ $product_size->getMaxLength() }}"
            attr-name="height"
            value="{{ old('height') }}"
            decimals="{{ $product_size->getDecimals() }}"
            :has-error="{{ json_encode(has_error('height') !== '') }}"
            help-text="{{ replace_el($product_size->getHelpText()) }}">
          </input-number-with-formatter>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="country_of_origin" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.products.country_of_origin') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="country_of_origin"
                 class="form-control text-center {{ has_error('country_of_origin') }}"
                 maxlength="{{ $country_code->getMaxLength() }}"
                 name="country_of_origin"
                 type="text"
                 value="{{ old('country_of_origin') }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($country_code->getHelpText()) }}">
        </div>
      </div>
    </div>
  </div>

  @include('_include.add_reserved_form')
  {{ csrf_field() }}
  {{ method_field('POST') }}
</form>
@endsection
