@extends('layout')

@section('title')
{{ __('view.order.order_list.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li><a href="{{ route('order.order_list.index') }}">{{ __('view.order.order_list.index') }}</a></li>
  <li>{{ __('view.order.order_list.edit') }}</li>
@endsection

@section('content')
<form id="save-order-form" class="form-horizontal basic-form save-data-form" action="{{ route('order.order_list.update', $order->order_number) }}" method="POST">
  <input type="hidden" name="updated_at" value="{{ $order->updated_at }}">
  <div class="row">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('order.order_list.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @if (! ($order->isCanceledOrder() || $order->factory_cancel_flag || $order->hadBeenShipped()))
        @canSave(Auth::user(), route_relatively('order.order_list.index'))
        <button class="btn btn-default btn-lg pull-right save-data" type="button">
          <i class="fa fa-save"></i> {{ __('view.global.save') }}
        </button>
        @endcanSave
      @endif
    </div>
  </div>

  <edit-order-data-form-first
    :order="{{ $order }}"
    :end-user="{{ $order->end_user }}"
    :delivery-destination="{{ $order->delivery_destination }}"
    :can-update-base="{{ json_encode(! $order->isAllocated() && ! $order->isFixedOrder()) }}"
    :can-update-shipping-date="{{ json_encode(! $order->isAllocated()) }}"
    :old-params="{{ json_encode(old() ?: new stdClass()) }}"
    :errors="{{ $errors->toJson() }}"
    :factory="{{ $order->factory }}"
    :customer="{{ $order->customer }}"
    :currencies="{{ $currencies }}"
    :statement-delivery-price-display-class-list="{{ $statement_delivery_price_display_class->toJson() }}"
    :basis-for-recording-sales-class-list="{{ $basis_for_recording_sales_class->toJson() }}"
    :small-peace-of-peper-type-code-list="{{ $small_peace_of_peper_type_code->toJson() }}">
  </edit-order-data-form-first>

  <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
      <h5>{{ __('view.order.order_list.fsystem_info') }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="slip_type" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.slip_type') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->slip_type->label() }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="creating_type" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.creating_type') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->creating_type->label() }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="slip_status_type" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.slip_status_type') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->slip_status_type->label() }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="factory_cancel_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.factory_cancel_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input name="factory_cancel_flag" type="checkbox" required="required" disabled {{ is_checked(1, old('factory_cancel_flag', $order->factory_cancel_flag)) }}>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="related_order_status_type" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.related_order_status_type') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->related_order_status_type->label() }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
      <h5>{{ __('view.order.order_list.returned_info') }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="returned_on" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.return_input.returned_on') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->returned_product->returned_on ?? '' }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="returned_product_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.products.product') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">
            {{ $order->isReturnedOrder() ? $order->returned_product->product->product_name : '' }}
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="returned_product_unit_price" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.unit_price') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">
            {{ $order->isReturnedOrder() ? $order->formatReturnedUnitPrice() : '' }}
          </span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="returned_product_quantity" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.return_input.returned_product_quantity') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->returned_product->quantity ?? '' }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="returned_product_unit_amount" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.order_amount') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">
            {{ $order->isReturnedOrder() ? $order->formatReturnedAmount() : '' }}
          </span>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="returned_product_remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.global.remark') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->returned_product->remark ?? '' }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
      <h5>{{ __('view.order.order_list.allocated_info') }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.shipment.product_allocations.allocate') }}{{ __('view.global.status') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $order->allocation_status->label() }}</span>
        </div>
      </div>
    </div>
  </div>

  <edit-order-data-form-second
    :order="{{ $order }}"
    :old-params="{{ json_encode(old() ?: new stdClass()) }}"
    :errors="{{ $errors->toJson() }}"
    :transport-companies="{{ $transport_companies }}">
  </edit-order-data-form-second>

  <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
      <h5>{{ __('view.order.order_list.other_info') }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="own_company_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.own_company_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="own_company_code"
            class="form-control ime-inactive {{ has_error('own_company_code') }}"
            name="own_company_code"
            type="text"
            value="{{ old('own_company_code', $order->own_company_code) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="organization_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.organization_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="organization_name"
            class="form-control ime-active {{ has_error('organization_name') }}"
            name="organization_name"
            type="text"
            value="{{ old('organization_name', $order->organization_name) }}">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="base_plus_end_user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.base_plus_end_user_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="base_plus_end_user_code"
            class="form-control ime-inactive {{ has_error('base_plus_end_user_code') }}"
            name="base_plus_end_user_code"
            type="text"
            value="{{ old('base_plus_end_user_code', $order->base_plus_end_user_code) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="customer_staff_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.customer_staff_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="customer_staff_name"
            class="form-control ime-active {{ has_error('customer_staff_name') }}"
            name="customer_staff_name"
            type="text"
            value="{{ old('customer_staff_name', $order->customer_staff_name) }}">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="purchase_staff_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.purchase_staff_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="purchase_staff_name"
            class="form-control ime-active {{ has_error('purchase_staff_name') }}"
            name="purchase_staff_name"
            type="text"
            value="{{ old('purchase_staff_name', $order->purchase_staff_name) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="place_order_work_staff_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.place_order_work_staff_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="place_order_work_staff_name"
            class="form-control ime-active {{ has_error('place_order_work_staff_name') }}"
            name="place_order_work_staff_name"
            type="text"
            value="{{ old('place_order_work_staff_name', $order->place_order_work_staff_name) }}">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="seller_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.seller_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="seller_name"
            class="form-control ime-active {{ has_error('seller_name') }}"
            name="seller_name"
            type="text"
            value="{{ old('seller_name', $order->seller_name) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="order_message" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.order.order_list.order_message') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="order_message"
            class="form-control ime-active {{ has_error('order_message') }}"
            name="order_message"
            type="text"
            value="{{ old('order_message', $order->order_message) }}">
        </div>
      </div>
    </div>
  </div>

  {{ method_field('PATCH') }}
  {{ csrf_field() }}
  @if (Auth::user()->canSave(route_relatively('order.order_list.index')) && ! ($order->isCanceledOrder() || $order->factory_cancel_flag || $order->hadBeenShipped()))
  <input id="can-save-data" type="hidden" value="1">
  @else
  <input id="can-save-data" type="hidden" value="0">
  @endcanSave
</form>
@endsection
