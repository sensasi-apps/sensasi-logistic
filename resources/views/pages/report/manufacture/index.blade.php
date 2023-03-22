@extends('layouts.main')

@section('title', __('report.name-report', ['name' => __('manufacture')]))

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">
@endpush

@section('main-content')
    <div class="section-body">
        <ul class="nav nav-pills nav-fill" role="tablist">
            <li class="nav-item">
                <a class="nav-link py-1" id="material-tab" data-toggle="tab" href="#material" role="tab"
                    aria-controls="material" aria-selected="false">
                    <i class="fas fa-box"></i>
                    {{ __('material') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link py-1" id="product-tab" data-toggle="tab" href="#product" role="tab"
                    aria-controls="product" aria-selected="false">
                    <i class="fas fa-boxes"></i>
                    {{ __('product') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="material" role="tabpanel" aria-labelledby="material-tab">
                @include('pages.report.manufacture._material-tab-content')
            </div>

            <div class="tab-pane fade" id="product" role="tabpanel" aria-labelledby="product-tab">
                @include('pages.report.manufacture._product-tab-content')
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        tab_system_init('manufactures', 'material');
    </script>
@endpush
