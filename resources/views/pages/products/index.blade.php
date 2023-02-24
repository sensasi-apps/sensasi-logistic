@extends('layouts.main')

@section('title', __('Product'))

@section('main-content')
    <div class="section-body">
        <ul id="pageTab" class="nav nav-pills nav-fill" role="tablist">
            <li class="nav-item">
                <a class="nav-link py-1" id="list-tab" data-toggle="tab" href="#list" role="tab"
                    aria-controls="{{ __('list') }}" aria-selected="false"><i class="fas fa-list"></i>
                    {{ __('Product List') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1" id="in-tab" data-toggle="tab" href="#in" role="tab"
                    aria-controls="{{ __('in') }}" aria-selected="false"><i class="fas fa-arrow-down"></i>
                    {{ __('Product In') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1" id="out-tab" data-toggle="tab" href="#out" role="tab"
                    aria-controls="{{ __('out') }}" aria-selected="false"><i class="fas fa-arrow-up"></i>
                    {{ __('Product Out') }}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="out" role="tabpanel" aria-labelledby="out-tab">
                @include('pages.products._product-outs')
            </div>
            <div class="tab-pane fade" id="in" role="tabpanel" aria-labelledby="in-tab">
                @include('pages.products._product-ins')
            </div>
            <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
                @include('pages.products._product-list')
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script>
        tab_system_init('products');
    </script>
@endpush
