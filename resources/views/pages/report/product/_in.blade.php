@include('components.assets._datatable')
@include('components.assets._select2')
@include('components.assets._datepicker')

<div id="productInsCrudDiv">
    <h2 class="section-title">
        {{ __('Product In Report') }}
        &mdash; {{ request()->get('label') ?? __('this month') }}
        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon" id="datepickerin">
            <i class="fas fa-calendar"></i> {{ __('Choose Date') }}
        </a>
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="card">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="note-tab" data-toggle="tab" data-target="#nota" type="button" role="tab" aria-controls="nota" aria-selected="true">Per Nota</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="item-tab" data-toggle="tab" data-target="#item" type="button" role="tab" aria-controls="item" aria-selected="false">Per Item</button>
                      </li>
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="nota" role="tabpanel" aria-labelledby="note-tab">
                        <a href="javascript:;" onclick="printDiv('productInsNoteTable', '{{ __('Product In Report') }}')"
                            class="btn btn-primary mb-3">
                            <i class="fas fa-print"></i> {{ __('Print') }}
                        </a>
                        @include('pages.report.product.inTable._nota')
                    </div>
                    <div class="tab-pane fade" id="item" role="tabpanel" aria-labelledby="item-tab">
                        <a href="javascript:;" onclick="printDiv('productInsItemTable', '{{ __('Product In Report') }}')"
                            class="btn btn-primary mb-3">
                            <i class="fas fa-print"></i> {{ __('Print') }}
                        </a>
                        @include('pages.report.product.inTable._item')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <div class="table-responsive d-none" id="productInsNoteTable">
        <h1 class="h2 my-5">{{ __('Product In Report') }} &mdash; {{ __('per note') }}</h1>
        @include('pages.report.product.inTable._nota')
    </div>

    <div class="table-responsive d-none table-sm" id="productInsItemTable">
        <h1 class="h2 my-5">{{ __('Product In Report') }} &mdash; {{ __('per item') }}</h1>
        @include('pages.report.product.inTable._item')
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
        if (productInsCrudDiv) {
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

                $('#datepickerin').daterangepicker({
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

