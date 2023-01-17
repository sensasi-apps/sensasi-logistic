@include('components.assets._datatable')
@include('components.assets._select2')
@include('components.assets._datepicker')

<div id="materialOutsCrudDiv">
    <h2 class="section-title">
        {{ __('Material Out List Report') }}
        &mdash; {{ request()->get('label') ?? __('this month') }}
        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon" id="datepickerout">
            <i class="fas fa-calendar"></i> {{ __('Choose Date') }}
        </a>
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="card">
                    <ul class="nav nav-tabs" id="outTable-tab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="notaOut-tab" data-toggle="tab" data-target="#notaOut" type="button" role="tab" aria-controls="notaOut" aria-selected="true">Per Nota</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="itemOut-tab" data-toggle="tab" data-target="#itemOut" type="button" role="tab" aria-controls="itemOut" aria-selected="false">Per Item</button>
                      </li>
                    </ul>
                </div>

                <div class="tab-content" id="myoutTable-tab">
                    <div class="tab-pane fade show active" id="notaOut" role="tabpanel" aria-labelledby="notaOut-tab">
                        <a href="javascript:;" onclick="printDiv('productInsNoteTable', '{{ __('Product In Report') }}')"
                            class="btn btn-primary mb-3">
                            <i class="fas fa-print"></i> {{ __('Print') }}
                        </a>
                        @include('pages.report.manufacture.outTable._nota')
                    </div>
                    <div class="tab-pane fade" id="itemOut" role="tabpanel" aria-labelledby="itemOut-tab">
                        <a href="javascript:;" onclick="printDiv('productInsItemTable', '{{ __('Product In Report') }}')"
                            class="btn btn-primary mb-3">
                            <i class="fas fa-print"></i> {{ __('Print') }}
                        </a>
                        @include('pages.report.manufacture.outTable._item')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <div class="table-responsive d-none" id="productInsNoteTable">
        <h1 class="h2 my-5">{{ __('Product In Report') }} &mdash; {{ __('per note') }}</h1>
        @include('pages.report.manufacture.outTable._nota')
    </div>

    <div class="table-responsive d-none table-sm" id="productInsItemTable">
        <h1 class="h2 my-5">{{ __('Product In Report') }} &mdash; {{ __('per item') }}</h1>
        @include('pages.report.manufacture.outTable._item')
    </div>

    <script>
        function printDiv(elementId, title) {
            const element = document.getElementById(elementId);
            element.querySelector('table').classList.add('table-sm');
            element.classList.add('print-only');
            element.classList.remove('d-none');

            window.print();

            element.classList.remove('print-only');
            element.classList.add('d-none');

        }
    </script>

    <script>
        if (materialOutsCrudDiv) {
            $(document).ready(function() {
                const dateRange = '{{ $_GET['daterange'] ?? '' }}'.split('_');
                const startDate = dateRange[0] ? moment(dateRange[0]) : moment().startOf('month');
                const endDate = dateRange[1] ? moment(dateRange[1]) : moment().endOf('month');

                function cb(start, end, label) {
                    const form = document.createElement('form');

                    dateRangeInput = document.createElement('input');
                    dateRangeInput.value = start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD');
                    dateRangeInput.name = 'daterange';

                    labelInput = document.createElement('input');
                    labelInput.value = label;
                    labelInput.name = 'label';

                    form.appendChild(dateRangeInput);
                    form.appendChild(labelInput);

                    document.querySelector('body').appendChild(form);
                    form.submit();
                }

                $('#datepickerout').daterangepicker({
                    autoUpdateInput: false,
                    startDate: startDate,
                    endDate: endDate,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    }
                }, cb);

            });
        }
    </script>
@endpush

