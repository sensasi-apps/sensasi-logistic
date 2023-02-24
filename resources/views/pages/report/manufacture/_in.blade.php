@include('components.assets._select2')
@include('components.assets._datepicker')

{{-- TODO: add report per categories --}}
{{-- TODO: reusable table component --}}

<div id="productInsCrudDiv">
    <h2 class="section-title">
        {{ __('Material In List Report') }}
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="card">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="note-tab" data-toggle="tab" data-target="#nota"
                                type="button" role="tab" aria-controls="nota" aria-selected="true">Per
                                Nota</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="item-tab" data-toggle="tab" data-target="#item" type="button"
                                role="tab" aria-controls="item" aria-selected="false">Per Item</button>
                        </li>
                    </ul>

                    <form method="get" action="{{ route('report.manufactures.index') }}">
                        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon"
                            id="datepickerin"><i class="fas fa-calendar"></i> Choose Date</a>
                        <input type="hidden" name="daterange" id="daterangein">
                        <button class="btn btn-info" type="submit">Filter</button>
                    </form>
                </div>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="nota" role="tabpanel" aria-labelledby="note-tab">
                        @include('pages.report.manufacture.inTable._nota')
                    </div>
                    <div class="tab-pane fade" id="item" role="tabpanel" aria-labelledby="item-tab">
                        @include('pages.report.manufacture.inTable._item')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        if (productInsCrudDiv) {
            $(function() {
                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#daterangein').val(start.format('YYYY-MM-D') + '_' + end.format('YYYY-MM-D'))
                    // console.log(start.format('YYYY MMMM D') + ' - ' + end.format('YYYY MMMM D'))
                }

                $('#datepickerin').daterangepicker({
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
