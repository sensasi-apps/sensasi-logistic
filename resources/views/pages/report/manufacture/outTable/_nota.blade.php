<table class="table table-striped table-bordered" id="materialInDatatable" style="width:100%" >
    <tr>
        <th>{{ __('Code') }}</th>
        <th>{{ __('At') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
    </tr>
    @if($materialOutDetailNota->count() != 0)            
        @foreach($materialOutDetailNota as $row)
            <?php $i=0 ?>
            
            @foreach($row->details as $detail)
            <?php $i+=1 ?>
                @if($i == 1)
                    <tr>
                        <td rowspan="{{$row->details->count()}}">{{$row->code}} </td>
                        <td rowspan="{{$row->details->count()}}">{{date_format($row->at, 'Y-m-d')}} </td>
                        <td>{{$detail->materialInDetail->material->name}} </td>
                        <td>{{$detail->qty}} {{$detail->materialInDetail->material->unit}}</td>
                    </tr>  
                @else
                    <tr>
                        <td>{{$detail->materialInDetail->material->name}} </td>
                        <td>{{$detail->qty}} </td>
                    </tr> 
                @endif
            @endforeach 
        @endforeach
    @else
        <tr align="center"><td colspan="4">{{ __('No data available in table') }}</td></tr>
    @endif
</table>