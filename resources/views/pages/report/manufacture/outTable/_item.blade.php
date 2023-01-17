<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
    </tr>
    @if($materialOutDetailItem->count() != 0)
        @foreach($materialOutDetailItem as $row)
            <tr>
                <td>{{$row->name}}</td>
                <td>{{$row->qty}} {{$row->unit}}</td>
            </tr>
        @endforeach
    @else
        <tr align="center"><td colspan="2">{{ __('No data available in table') }}</td></tr>
    @endif
</table>