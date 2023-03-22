@include('components.alpine-data._report')

<div x-data="reportComponent">
    <h2 class="section-title">
        {{ $tab }}
        &mdash; {{ __(request()->get('label')) ?? __('this month') }}
        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon ml-2" x-init="daterangepicker">
            <i class="fas fa-calendar"></i> {{ __('Choose Date') }}
        </a>
    </h2>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        @foreach ($subtabs as $subtabKey => $subtab)
            <li class="nav-item" role="presentation">
                <button id="{{ $key }}-{{ $subtabKey }}-tab" data-toggle="tab" type="button"
                    @class(['nav-link', 'active' => $loop->first]) data-target="#{{ $key }}-{{ $subtabKey }}"
                    aria-controls="{{ $key }}-{{ $subtabKey }}"
                    aria-selected="{{ $loop->first ? 'true' : 'false' }}" role="tab">
                    {{ $subtab }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="card">
        <div class="card-body">
            <div class="tab-content">
                @foreach ($subtabs as $subtabKey => $subtab)
                    <div @class(['tab-pane fade show', 'active' => $loop->first]) id="{{ $key }}-{{ $subtabKey }}" role="tabpanel"
                        aria-labelledby="{{ $key }}-{{ $subtabKey }}-tab">
                        <button type="button"
                            @@click="printTable(
                                '{{ __('report.name-report', ['name' => $tab]) }}',
                                '{{ __(request()->get('label')) ?? __('this month') }} - {{ $subtab }}'
                                )"
                            class="btn btn-primary mb-3 icon-left btn-icon text-capitalize">
                            <i class="fas fa-print"></i> {{ __('print') }}
                        </button>

                        <div class="table-responsive">
                            @include("pages.report.components._{$reportPageId}-{$key}-by-{$subtabKey}-table")
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
