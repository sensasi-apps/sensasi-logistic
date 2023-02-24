<table class="table table-striped table-bordered" style="width:100%">
    <tr>
        <th>{{ __('Code') }}</th>
        <th>{{ __('At') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }}</th>
        <th>{{ __('Total') }}</th>
        <th>{{ __('Type') }}</th>
    </tr>

    @foreach ($productOutDetailNota as $row)
        <?php $i = 0; ?>
        @foreach ($row->details as $detail)
            <?php $i += 1; ?>
            @if ($i == 1)
                <tr>
                    <td rowspan="{{ $row->details->count() }}">{{ $row->code }} </td>
                    <td rowspan="{{ $row->details->count() }}">{{ date_format(new DateTime($row->at), 'Y-m-d') }}</td>
                    <td>{{ $detail->productInDetail->product->name }}</td>
                    <td>{{ $detail->qty }} {{ $detail->productInDetail->product->unit }}</td>
                    <td>Rp. {{ $detail->price }}</td>
                    <td>Rp. {{ $detail->price * $detail->qty }}</td>
                    <td rowspan="{{ $row->details->count() }}">{{ $row->type }}</td>
                </tr>
            @else
                <tr>
                    <td>{{ $detail->productInDetail->product->name }}</td>
                    <td>{{ $detail->qty }} {{ $detail->productInDetail->product->unit }}</td>
                    <td>Rp. {{ $detail->price }}</td>
                    <td>Rp. {{ $detail->price * $detail->qty }}</td>
                </tr>
            @endif
        @endforeach
    @endforeach
</table>
