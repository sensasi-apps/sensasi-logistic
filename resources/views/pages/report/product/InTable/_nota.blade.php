<table class="table table-striped table-bordered" id="productInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Code') }}</th>
        <th>{{ __('At') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Type') }}</th>
    </tr>
    
    @if($productInDetailNota->count() != 0)
        @foreach($productInDetailNota as $row)
            <?php $i=0 ?>
            @foreach($row->details as $detail)
            <?php $i+=1 ?>
                @if($i == 1)
                <tr>
                        <td rowspan="{{$row->details->count()}}">{{$row->code}} </td>
                        <td rowspan="{{$row->details->count()}}">{{date_format(new DateTime($row->at), 'Y-m-d')}}</td>
                        <td>{{$detail->product->name}}</td>
                        <td>{{$detail->qty}} {{$detail->product->unit}}</td>
                        <td rowspan="{{$row->details->count()}}">{{$row->type}}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{$detail->product->name}}</td>
                        <td>{{$detail->qty}} {{$detail->product->unit}}</td>
                    </tr>
                @endif
            @endforeach
                            
        @endforeach
    @else
        <tr align="center"><td colspan="5">{{ __('No data available in table') }}</td></tr>
    @endif
</table>