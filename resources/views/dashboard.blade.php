@extends('layouts.main')

@section('title', __('Dashboard'))

@include('components.assets._datatable')
@include('components.assets._alpinejs')

{{-- TODO: implement alpinejs --}}

@push('css-lib')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('css')
    <style>
        .card-stats-item {
            width: unset !important;
        }
    </style>
@endpush

@push('js-lib')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"
        integrity="sha512-CTiTx27lUxqoBGKfEHj2giGQTRdWgwJHNixfAOzPo5Hb86I03/YwYt+wpTM2TjFGespwSgQwUWKtLHPt2zTTDA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('js')
    <script>
        let products;
        let materials = [];
        let productDatatable = document.getElementById('productDatatable');
        let materialDatatable = document.getElementById('materialDatatable');

        const productTagSearch = tag => productDatatable.search(tag).draw();
        const materialTagSearch = tag => materialDatatable.search(tag).draw();

        (function() {
            $('.owl-carousel').owlCarousel({
                // loop: true,
                margin: 0,
                // nav: true
                responsive: {
                    0: {
                        items: 4
                    },
                    576: {
                        items: 3
                    },
                    991: {
                        items: 2
                    }
                }
            })

            materialDatatable = $(materialDatatable).DataTable({
                processing: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                },
                serverSide: true,


                ajax: {
                    url: '{{ route('api.datatable', ['model_name' => 'Material']) }}',
                    dataSrc: json => {
                        materials = json.data
                        return json.data
                    },
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            "Authorization",
                            'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                        )
                    },
                    cache: true,
                    accept: 'application/json'
                },
                order: [],
                columns: [{
                    data: 'code',
                    title: '{{ __('Code') }}'
                }, {
                    data: 'name',
                    title: '{{ __('Name') }}'
                }, {
                    data: 'qty',
                    title: '{{ __('Qty') }}',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row) => `${data} ${row.unit}`
                }, {
                    data: 'tags',
                    name: 'tags_json',
                    title: '{{ __('Tags') }}',
                    render: data => data?.map(tag =>
                        `<a href="#/" class="m-1 badge badge-primary" onclick="materialTagSearch('${tag}')">${tag}</a>`
                    ).join('') || null,
                }]
            });

            productDatatable = $(productDatatable).DataTable({
                processing: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('api.datatable', ['model_name' => 'Product']) }}',
                    dataSrc: json => {
                        products = json.data;
                        return json.data;
                    },
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            "Authorization",
                            'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                        )
                    },
                    cache: true
                },
                columns: [{
                    data: 'code',
                    title: '{{ __('validation.attributes.code') }}'
                }, {
                    data: 'name',
                    title: '{{ __('validation.attributes.name') }}'
                }, {
                    data: 'qty',
                    title: '{{ __('Qty') }}',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row) => `${data} ${row.unit}`
                }, {
                    data: 'default_price',
                    title: '{{ __('validation.attributes.default_price') }}',
                    render: data => data.toLocaleString()
                }, {
                    data: 'tags',
                    name: 'tags_json',
                    title: '{{ __('Tags') }}',
                    render: data => data?.map(tag =>
                        `<a href="#/" onclick="productTagSearch('${tag}')" class="m-1 badge badge-primary">${tag}</a>`
                    ).join('') || null,
                }]
            });
        })();
    </script>
@endpush

@section('main-content')

    <h2 class="section-title">
        {{ __('Assets In/Out') }}

        <div class="dropdown d-inline ml-4">
            <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#"
                id="orders-month">{{ $months[$currentMonth - 1] }}</a>
            <ul class="dropdown-menu dropdown-menu-sm">
                <li class="dropdown-title">Select Month</li>

                @foreach ($months as $i => $month)
                    <li><a href="?month={{ $i + 1 }}"
                            class="dropdown-item{{ $currentMonth - 1 === $i ? ' active' : '' }}">{{ $month }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </h2>

    <div class="row">
        @foreach ($stats as $key1 => $stat)
            @foreach ($stat as $key2 => $item)
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-2">
                        <div class="card-stats">
                            <div class="card-stats-title">
                                {{ __("{$key1} {$key2}") }}
                            </div>
                            <div class="card-stats-items owl-carousel">
                                @foreach ($item['nCategories'] as $nCat)
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count">{{ $nCat->count }}</div>
                                        <div class="card-stats-item-label">{{ $nCat->type }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div
                            class="card-icon shadow-{{ $item['color'] ?? 'secondary' }} bg-{{ $item['color'] ?? 'secondary' }}">
                            <i class="fas fa-{{ $key1 === 'material' ? 'seedling' : 'box' }}"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Total') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $item['total'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

    <h2 class="section-title">
        {{ __('Current Assets') }}
    </h2>

    <div class="row">
        @foreach ($worths as $key => $item)
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card card-statistic-2">
                    <div class="card-icon shadow-success bg-success">
                        <span class="text-white">{{ __('$') }}</span>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __($key) }}</h4>
                        </div>
                        <div class="card-body">
                            @number($item)
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card" x-data="{ isOpen: window.innerWidth > 768 }"
                @resize.window="
            width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
            isOpen = width > 768;">
                <div role="button" class="card-header hoverable" @@click="isOpen = !isOpen">
                    <h4>{{ __('Material List') }}</h4>
                </div>
                <div class="card-body" x-show="isOpen" x-transition>
                    <div class="table-responsive">
                        <table class="table table-striped" id="materialDatatable" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="card" x-data="{ isOpen: window.innerWidth > 768 }"
                @resize.window="
            width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
            isOpen = width > 768;">
                <div role="button" class="card-header hoverable" @@click="isOpen = !isOpen">
                    <h4>{{ __('Product List') }}</h4>
                </div>
                <div class="card-body" x-show="isOpen" x-transition>
                    <div class="table-responsive">
                        <table class="table table-striped" id="productDatatable" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
