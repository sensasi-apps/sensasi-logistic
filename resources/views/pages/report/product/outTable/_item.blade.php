<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }}</th>
        <th>{{ __('Total') }}</th>
    </tr>
                    
    @foreach($productOutDetailItem as $row)
        <tr>
            <td>{{$row->name}}</td>
            <td>{{$row->qty}} {{$row->unit}}</td>
            <td>{{$row->total/$row->qty}}</td>
            <td>{{$row->total}}</td>
        </tr>
                        
    @endforeach
</table>