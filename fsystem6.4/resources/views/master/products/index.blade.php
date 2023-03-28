@extends('layout')

@section('title')
{{ __('view.master.products.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.products.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.products.search') }}" method="POST">
    <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.species.species') }}</th>
          <td>
            <select class="form-control {{ has_error('species_code') }}" name="species_code">
              <option value=""></option>
              @foreach ($species as $s)
              <option value="{{ $s->species_code }}" {{ is_selected($s->species_code, $params['species_code'] ?? '') }}>
                {{ $s->species_name }}
              </option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.products.product_code') }}</th>
          <td>
            <input class="form-control {{ has_error('product_code') }}" maxlength="{{ $product_code->getMaxLength() }}" name="product_code" value="{{ old('product_code', $params['product_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.products.product_name') }}</th>
          <td>
            <input class="form-control {{ has_error('product_name') }}" maxlength="40" name="product_name" value="{{ old('product_name', $params['product_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-2 col-sm-offset-2">
      @canSave(Auth::user(), route_relatively('master.products.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.products.add') }}">
        <i class="fa fa-plus"></i> {{ __('view.global.add') }}
      </a>
      @endcanSave
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
      <span class="search-result-count">{{ $products->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $products->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.products.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('species.species_name', __('view.master.species.species'))</th>
          <th>@sortablelink('product_code', __('view.master.products.product_code'))</th>
          <th>@sortablelink('product_name', __('view.master.products.product_name'))</th>
          <th>{{ __('view.master.global.creating_type') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $p)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.products.edit', $p->product_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.products.index'))
          <td>
            @if ($p->isDeletable())
            <delete-form route-action="{{ route('master.products.delete', $p->product_code) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $p->species->species_name }}</td>
          <td>{{ $p->product_code }}</td>
          <td class="text-left">{{ $p->product_name }}</td>
          <td class="text-left">{{ $p->creating_type->label() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
