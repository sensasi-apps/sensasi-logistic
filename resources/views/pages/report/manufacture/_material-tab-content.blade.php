@include('components.alpine-data._report')

@php
    $subtitle = __('report.name-report', ['name' => __('manufacture') . ' ' . __('material')]);
@endphp

<div x-data="reportComponent">
    <h2 class="section-title">
        {{ $subtitle }}
        &mdash; {{ __(request()->get('label')) ?? __('this month') }}
        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon ml-2" x-init="daterangepicker">
            <i class="fas fa-calendar"></i> {{ __('Choose Date') }}
        </a>
    </h2>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button id="material-manufacture-invoice-tab" data-toggle="tab" type="button" @class(['nav-link', 'active', 'text-capitalize'])
                data-target="#material-manufacture-invoice" aria-controls="material-manufacture-invoice"
                aria-selected="true" role="tab">
                {{ __('by invoice') }}
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button id="material-manufacture-out-item-tab" data-toggle="tab" type="button" @class(['nav-link text-capitalize'])
                data-target="#material-manufacture-out-item" aria-controls="material-manufacture-out-item"
                aria-selected="false" role="tab">
                {{ __('material outs') }}
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button id="material-manufacture-in-item-tab" data-toggle="tab" type="button" @class(['nav-link text-capitalize'])
                data-target="#material-manufacture-in-item" aria-controls="material-manufacture-in-item"
                aria-selected="false" role="tab">
                {{ __('material ins') }}
            </button>
        </li>
    </ul>

    <div class="card">
        <div class="card-body">
            <div class="tab-content">
                <div @class(['tab-pane fade show', 'active']) id="material-manufacture-invoice" role="tabpanel"
                    aria-labelledby="material-manufacture-invoice-tab">
                    <button type="button"
                        @@click="printTable(
							'{{ $subtitle }}',
							'{{ __(request()->get('label')) ?? __('this month') }} - {{ __('by invoice') }}'
							)"
                        class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                        <i class="fas fa-print"></i> {{ __('print') }}
                    </button>

                    <div class="table-responsive">
                        @include('pages.report.components._manufacture-material-by-invoice-table')
                    </div>
                </div>

                <div @class(['tab-pane fade']) id="material-manufacture-out-item" role="tabpanel"
                    aria-labelledby="material-manufacture-out-item-tab">
                    <button type="button"
                        @@click="printTable(
							'{{ __('report.name-report', ['name' => __('manufacture')]) }}',
							'{{ __(request()->get('label')) ?? __('this month') }} - {{ __('material out') }}'
							)"
                        class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                        <i class="fas fa-print"></i> {{ __('print') }}
                    </button>

                    <div class="table-responsive">
                        @include('pages.report.components._material-out-by-item-table', [
                            'materialOutDetailsGroupByMaterial' => $materialManufactureMaterialOutDetailsGroupByMaterial,
                        ])
                    </div>
                </div>

                <div @class(['tab-pane fade']) id="material-manufacture-in-item" role="tabpanel"
                    aria-labelledby="material-manufacture-in-item-tab">
                    <button type="button"
                        @@click="printTable(
							'{{ __('report.name-report', ['name' => __('manufacture')]) }}',
							'{{ __(request()->get('label')) ?? __('this month') }} - {{ __('material in') }}'
							)"
                        class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                        <i class="fas fa-print"></i> {{ __('print') }}
                    </button>

                    <div class="table-responsive">
                        @include('pages.report.components._material-in-by-item-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
