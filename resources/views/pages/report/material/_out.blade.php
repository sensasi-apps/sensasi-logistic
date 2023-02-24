@include('components.assets._select2')
@include('components.assets._datepicker')

<div id="materialOutsCrudDiv">
    <h2 class="section-title">
        {{ __('Material Out List Report') }}
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="card">
                    <ul class="nav nav-tabs" id="outTable-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="notaOut-tab" data-toggle="tab" data-target="#notaOut"
                                type="button" role="tab" aria-controls="notaOut" aria-selected="true">Per
                                Nota</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="itemOut-tab" data-toggle="tab" data-target="#itemOut"
                                type="button" role="tab" aria-controls="itemOut" aria-selected="false">Per
                                Item</button>
                        </li>
                    </ul>

                    <form method="get" action="{{ route('report.materials.index') }}#out">
                        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon"
                            id="datepickerout"><i class="fas fa-calendar"></i> Choose Date</a>
                        <input type="hidden" name="daterange" id="daterangeout">
                        <button class="btn btn-info" type="submit">Filter</button>
                    </form>
                </div>

                <div class="tab-content" id="myoutTable-tab">
                    <div class="tab-pane fade show active" id="notaOut" role="tabpanel" aria-labelledby="notaOut-tab">
                        @include('pages.report.material.outTable._nota')
                    </div>
                    <div class="tab-pane fade" id="itemOut" role="tabpanel" aria-labelledby="itemOut-tab">
                        @include('pages.report.material.outTable._item')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        if (materialOutsCrudDiv) {
            $(function() {
                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#daterangeout').val(start.format('YYYY-MM-D') + '_' + end.format('YYYY-MM-D'))
                }

                $('#datepickerout').daterangepicker({
                    autoUpdateInput: false,
                    startDate: start,
                    endDate: end,
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

                cb(start, end);
            });
        }
    </script>
@endpush
