@extends('layouts.main')

@section('title', ucfirst(__('product')))

@section('main-content')
    <div class="section-body">
        <ul id="pageTab" class="nav nav-pills nav-fill text-capitalize flex-nowrap" role="tablist">
            @hasanyrole(['Super Admin', 'Warehouse'])
                <li class="nav-item">
                    <a class="nav-link py-1" id="list-tab" data-toggle="tab" href="#list" role="tab"
                        aria-controls="{{ __('list') }}" aria-selected="false"><i class="fas fa-list"></i>
                        {{ __('Product List') }}</a>
                </li>
            @endhasanyrole

            @hasanyrole(['Super Admin', 'Warehouse', 'Purchase'])
                <li class="nav-item">
                    <a class="nav-link py-1" id="in-tab" data-toggle="tab" href="#in" role="tab"
                        aria-controls="{{ __('in') }}" aria-selected="false"><i class="fas fa-arrow-down"></i>
                        {{ __('product in') }}</a>
                </li>
            @endhasanyrole

            @hasanyrole(['Super Admin', 'Warehouse', 'Sales'])
                <li class="nav-item">
                    <a class="nav-link py-1" id="out-tab" data-toggle="tab" href="#out" role="tab"
                        aria-controls="{{ __('out') }}" aria-selected="false"><i class="fas fa-arrow-up"></i>
                        {{ __('product out') }}</a>
                </li>
            @endhasanyrole
        </ul>

        <div class="tab-content">
            @hasanyrole(['Super Admin', 'Warehouse'])
                <div class="tab-pane fade" id="out" role="tabpanel" aria-labelledby="out-tab">
                    @include('pages.products._product-outs')
                </div>
            @endhasanyrole


            @hasanyrole(['Super Admin', 'Warehouse', 'Purchase'])
                <div class="tab-pane fade" id="in" role="tabpanel" aria-labelledby="in-tab">
                    @include('pages.products._product-ins')
                </div>
            @endhasanyrole


            @hasanyrole(['Super Admin', 'Warehouse', 'Sales'])
                <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
                    @include('pages.products._product-list')
                </div>
            @endhasanyrole
        </div>
    </div>
@endsection

@push('js')
    <script>
        tab_system_init('products');
    </script>
@endpush
