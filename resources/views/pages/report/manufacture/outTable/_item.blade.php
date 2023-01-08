<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
    </tr>

    @foreach($materialOutDetailItem as $row)
        <tr>
            <td>{{$row->name}}</td>
            <td>{{$row->qty}} {{$row->unit}}</td>
        </tr>
    @endforeach
</table>