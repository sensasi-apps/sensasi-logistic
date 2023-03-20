@extends('layouts.main')

@section('title', ucfirst(__('manufacture')))

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('main-content')
    <div class="section-body">
        <ul id="pageTab" class="nav nav-pills nav-fill text-capitalize flex-nowrap" role="tablist">
            <li class="nav-item">
                <a class="nav-link py-1" id="product-tab" data-toggle="tab" href="#product" role="tab"
                    aria-controls="{{ __('product') }}" aria-selected="false"><i class="fas fa-pallet"></i>
                    {{ __('product') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1" id="material-tab" data-toggle="tab" href="#material" role="tab"
                    aria-controls="{{ __('material') }}" aria-selected="false"><i class="fas fa-box"></i>
                    {{ __('material') }}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="material" role="tabpanel" aria-labelledby="material-tab">
                @include('pages.manufacture._materials-tab.index')
            </div>
            <div class="tab-pane fade" id="product" role="tabpanel" aria-labelledby="product-tab">
                @include('pages.manufacture._products-tab.index')
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        tab_system_init('manufactures', 'product');
    </script>
@endpush
