@extends('layouts.main')

@section('title', $title)

@include('components.alpine-data._report')


@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">
@endpush

@section('main-content')
    <div class="section-body">
        <div x-data="reportComponent">
            <h2 class="section-title">
                {{ $tab }}
                &mdash; {{ request()->get('label') ?? __('this month') }}
                <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon ml-2" x-init="daterangepicker">
                    <i class="fas fa-calendar"></i> {{ __('Choose Date') }}
                </a>
            </h2>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button id="manufacture-invoice-tab" data-toggle="tab" type="button" @class(['nav-link', 'active', 'text-capitalize'])
                        data-target="#manufacture-invoice" aria-controls="manufacture-invoice" aria-selected="true"
                        role="tab">
                        {{ __('by invoice') }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button id="out-item-tab" data-toggle="tab" type="button" @class(['nav-link text-capitalize'])
                        data-target="#out-item" aria-controls="out-item" aria-selected="false" role="tab">
                        {{ __('material outs') }}
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button id="in-item-tab" data-toggle="tab" type="button" @class(['nav-link text-capitalize'])
                        data-target="#in-item" aria-controls="in-item" aria-selected="false" role="tab">
                        {{ __('product ins') }}
                    </button>
                </li>
            </ul>

            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div @class(['tab-pane fade show', 'active']) id="manufacture-invoice" role="tabpanel"
                            aria-labelledby="manufacture-invoice-tab">
                            <button type="button"
                                @@click="printTable(
                                '{{ __('report.name-report', ['name' => $tab]) }}',
                                '{{ request()->get('label') ?? __('this month') }} - {{ __('by invoice') }}'
                                )"
                                class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                                <i class="fas fa-print"></i> {{ __('print') }}
                            </button>

                            <div class="table-responsive">
                                @include('pages.report.components._manufacture--by-invoice-table')
                            </div>
                        </div>

                        <div @class(['tab-pane fade show']) id="out-item" role="tabpanel" aria-labelledby="out-item-tab">
                            <button type="button"
                                @@click="printTable(
                                '{{ __('report.name-report', ['name' => __('manufacture')]) }}',
                                '{{ request()->get('label') ?? __('this month') }} - {{ __('material out') }}'
                                )"
                                class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                                <i class="fas fa-print"></i> {{ __('print') }}
                            </button>

                            <div class="table-responsive">
                                @include('pages.report.components._material-out-by-item-table')
                            </div>
                        </div>

                        <div @class(['tab-pane fade show']) id="in-item" role="tabpanel" aria-labelledby="in-item-tab">
                            <button type="button"
                                @@click="printTable(
                                '{{ __('report.name-report', ['name' => __('manufacture')]) }}',
                                '{{ request()->get('label') ?? __('this month') }} - {{ __('product in') }}'
                                )"
                                class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                                <i class="fas fa-print"></i> {{ __('print') }}
                            </button>

                            <div class="table-responsive">
                                @include('pages.report.components._product-in-by-item-table')
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
