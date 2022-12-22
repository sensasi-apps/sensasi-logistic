@include('components.assets._datatable')
@include('components.assets._select2')
@include('components.assets._datepicker')

<div id="materialInsCrudDiv">
    <h2 class="section-title">
        {{ __('Material In List Report') }}
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="card">
                    <form method="get" action="{{route('report.materials.index')}}">
                        <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon" id="datepicker"><i class="fas fa-calendar"></i> Choose Date</a>
                        <input type="hidden" name="daterange" id="daterange">
                        <input type="hidden" name="#in" value="#in">
                        <button class="btn btn-info" type="submit">Filter</button>
                    </form>
                </div>

                <table class="table table-striped" id="materialInDatatable" style="width:100%">
                    <tr>
                        <th>{{ __('At') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Type') }}</th>
                    </tr>

                    @foreach($materialInDetail as $row)
                        <tr>
                            <td>{{date_format($row->materialIn->at, 'Y-m-d')}}</td>
                            <td>{{$row->material->name}}</td>
                            <td>{{$row->qty}} {{$row->material->unit}}</td>
                            <td>Rp. {{$row->price}}</td>
                            <td>Rp. {{$row->price*$row->qty}}</td>
                            <td>{{$row->materialIn->type}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')

    <script>
        if (materialInsCrudDiv) {
            $( function() {
                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#daterange').val(start.format('YYYY-MM-D') + '_' + end.format('YYYY-MM-D'))
                    // console.log(start.format('YYYY MMMM D') + ' - ' + end.format('YYYY MMMM D'))
                }

                $('#datepicker').daterangepicker({
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

