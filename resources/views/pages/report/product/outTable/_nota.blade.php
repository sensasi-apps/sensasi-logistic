<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Code') }}</th>
        <th>{{ __('At') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }} {{__('($)')}}</th>
        <th>{{ __('Total') }} {{__('($)')}}</th>
        <th>{{ __('Type') }}</th>
    </tr>
    <?php $total = 0 ?>
                    
    @if($productOutDetailNota->count() != 0)
        @foreach($productOutDetailNota as $row)
            <?php $i=0 ?>
            @foreach($row->details as $detail)
            <?php $i+=1 ?>
                @if($i == 1)
                <tr>
                        <td rowspan="{{$row->details->count()}}">{{$row->code}} </td>
                        <td rowspan="{{$row->details->count()}}">{{date_format(new DateTime($row->at), 'Y-m-d')}}</td>
                        <td>{{$detail->productInDetail->product->name}}</td>
                        <td>{{$detail->qty}} {{$detail->productInDetail->product->unit}}</td>
                        <td>{{number_format($detail->price,0,',','.')}}</td>
                        <td>{{number_format($detail->price*$detail->qty,0,',','.')}}</td>
                        <td rowspan="{{$row->details->count()}}">{{$row->type}}</td>
                        <?php $total += $detail->price*$detail->qty ?>
                    </tr>
                @else
                    <tr>
                        <td>{{$detail->productInDetail->product->name}}</td>
                        <td>{{$detail->qty}} {{$detail->productInDetail->product->unit}}</td>
                        <td>{{number_format($detail->price,0,',','.')}}</td>
                        <td>{{number_format($detail->price*$detail->qty,0,',','.')}}</td>
                        <?php $total += $detail->price*$detail->qty ?>
                    </tr>
                @endif
            @endforeach
                            
        @endforeach

        <tr>
            <th colspan="5">Total</th>
            <th colspan="2">{{number_format($total,0,',','.')}}</th>
        </tr>

    @else
        <tr align="center">
            <td colspan="7">{{ __('No data available in table') }}</td>
        </tr>
    @endif

    
</table>