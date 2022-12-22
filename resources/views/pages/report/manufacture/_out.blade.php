@include('components.assets._datatable')
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
                    <form method="get" action="{{route('report.manufactures.index')}}#out">
                        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon" id="datepickerout"><i class="fas fa-calendar"></i> Choose Date</a>
                        <input type="hidden" name="daterange" id="daterangeout">
                        <button class="btn btn-info" type="submit">Filter</button>
                    </form>
                </div>

                <table class="table table-striped" id="materialInDatatable" style="width:100%">
                    <tr>
                        <th>{{ __('At') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Type') }}</th>
                    </tr>

                    @foreach($materialOutDetail as $row)
                        <tr>
                            <td>{{date_format($row->materialOut->at, 'Y-m-d')}}</td>
                            <td>{{$row->materialInDetail->material->name}}</td>
                            <td>{{$row->qty}} {{$row->materialInDetail->material->unit}}</td>
                            <td>{{$row->materialOut->type}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')

    <script>
        if (materialOutsCrudDiv) {
            $( function() {
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
                       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

                cb(start, end);
              });
        }
    </script>
@endpush

