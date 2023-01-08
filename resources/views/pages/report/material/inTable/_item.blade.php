<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }}</th>
        <th>{{ __('Total') }}</th>
    </tr>
                    
    @foreach($materialInDetailItem as $row)
        <tr>
            <td>{{$row->name}}</td>
            <td>{{$row->qty}} {{$row->unit}}</td>
            <td>Rp. {{number_format($row->total/$row->qty,2,',','.')}}</td>
            <td>Rp. {{number_format($row->total,2, ',', '.')}}</td>
        </tr>
                        
    @endforeach
</table>