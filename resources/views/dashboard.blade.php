@extends('layouts.main')

@section('title', __('Dashboard'))

@include('components.assets._alpinejs')
@include('components.alpine-data._datatable')

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
                            <div class="card-stats-title text-capitalize">
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
            <div class="card" x-data="{ isOpen: window.innerWidth > 768 }">
                <div role="button" class="card-header hoverable" @@click="isOpen = !isOpen">
                    <h4>{{ __('Material List') }}</h4>
                </div>
                <div class="card-body" x-show="isOpen" x-transition>
                    <div class="table-responsive">
                        <table x-data="dataTable(materialListDataTableConfig)" class="table table-striped" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="card" x-data="{ isOpen: window.innerWidth > 768 }">
                <div role="button" class="card-header hoverable" @@click="isOpen = !isOpen">
                    <h4>{{ __('Product List') }}</h4>
                </div>
                <div class="card-body" x-show="isOpen" x-transition>
                    <div class="table-responsive">
                        <table x-data="dataTable(productListDataTableConfig)" class="table table-striped" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        const materialListDataTableConfig = {
            setDataListEventName: 'material:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ route('api.datatable', ['model_name' => 'Material']) }}',
            order: [2, 'asc'],
            columns: [{
                data: 'code',
                title: '{{ __('validation.attributes.code') }}'
            }, {
                data: 'brand',
                title: '{{ __('validation.attributes.brand') }}'
            }, {
                data: 'name',
                title: '{{ __('validation.attributes.name') }}'
            }, {
                data: 'qty',
                title: '{{ __('validation.attributes.qty') }}',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {

                    const isStockLow = row.low_qty && row.qty <= row.low_qty;

                    return isStockLow ?
                        `<b class="text-${row.qty === 0 ? 'danger' : 'warning'} blinking" x-init="$($el).tooltip()" title="${row.qty === 0 ? '{{ __('out of stock') }}' : '{{ __('low qty') }}'}">${data} ${row.unit}</b>` :
                        `${data} ${row.unit}`;
                }
            }, {
                data: 'tags',
                name: 'tags_json',
                width: '15%',
                orderable: false,

                title: '{{ __('validation.attributes.tags') }}',
                render: data => data?.map(tag =>
                    `<a href="javascript:;" class="m-1 badge badge-primary" @click="search('${tag}')">${tag}</a>`
                ).join(''),
            }]
        };

        const productListDataTableConfig = {
            setDataListEventName: 'product:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ route('api.datatable', ['model_name' => 'Product']) }}',
            order: [1, 'asc'],
            columns: [{
                data: 'code',
                title: '{{ __('validation.attributes.code') }}'
            }, {
                data: 'name',
                title: '{{ __('validation.attributes.name') }}'
            }, {
                data: 'qty',
                title: '{{ __('validation.attributes.qty') }}',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {

                    const isStockLow = row.low_qty && row.qty <= row.low_qty;

                    return isStockLow ?
                        `<b class="text-${row.qty === 0 ? 'danger' : 'warning'} blinking" x-init="$($el).tooltip()" title="${row.qty === 0 ? '{{ __('out of stock') }}' : '{{ __('low qty') }}'}">${data} ${row.unit}</b>` :
                        `${data} ${row.unit}`;
                }
            }, {
                data: 'tags',
                name: 'tags_json',
                width: '15%',
                orderable: false,

                title: '{{ __('validation.attributes.tags') }}',
                render: data => data?.map(tag =>
                    `<a href="javascript:;" class="m-1 badge badge-primary" @click="search('${tag}')">${tag}</a>`
                ).join(''),
            }]
        };

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
        })();
    </script>
@endpush
