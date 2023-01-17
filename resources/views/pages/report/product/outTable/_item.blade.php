<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }} {{__('($)')}}</th>
        <th>{{ __('Total') }} {{__('($)')}}</th>
    </tr>

    <?php $total = 0 ?>
    
    @if($productOutDetailItem->count() != 0)
        @foreach($productOutDetailItem as $row)
            <tr>
                <td>{{$row->name}}</td>
                <td>{{$row->qty}} {{$row->unit}}</td>
                <td>{{number_format($row->total/$row->qty,0,",",".")}}</td>
                <td>{{number_format($row->total,0,",",".")}}</td>
                <?php $total += $row->total ?>
            </tr>      
        @endforeach
        <tr>
            <th colspan="3">Total</th>
            <th colspan="">{{number_format($total,0,',','.')}}</th>
        </tr>
    @else
        <tr align="center"><td colspan="4">{{ __('No data available in table') }}</td></tr>
    @endif
</table>