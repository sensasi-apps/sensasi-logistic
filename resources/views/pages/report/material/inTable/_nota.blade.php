<table class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>{{ __('Code') }}</th>
            <th>{{ __('At') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Quantity') }}</th>
            <th>{{ __('Price') }} ({{ __('$') }})</th>
            <th>{{ __('Subtotal') }} ({{ __('$') }})</th>
            <th>{{ __('Type') }}</th>
        </tr>
    </thead>

    @if ($materialIns->count() == 0)

        <tbody>
            <tr>
                <td colspan="7" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>

            @foreach ($materialIns as $materialIn)
                @foreach ($materialIn->details as $detail)
                    @if ($loop->first)
                        <tr>
                            <td rowspan="{{ $materialIn->details->count() }}">{{ $materialIn->code }} </td>
                            <td rowspan="{{ $materialIn->details->count() }}">
                                {{ date_format($materialIn->at, 'd-m-Y') }}</td>
                            <td>{{ $detail->material->name }} {{ $detail->material->brand }}</td>
                            <td>@number($detail->qty) {{ $detail->material->unit }}</td>
                            <td>@number($detail->price)</td>
                            <td>@number($detail->qty * $detail->price)</td>
                            <td rowspan="{{ $materialIn->details->count() }}">{{ $materialIn->type }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $detail->material->name }} {{ $detail->material->brand }}</td>
                            <td>@number($detail->qty) {{ $detail->material->unit }}</td>
                            <td>@number($detail->price)</td>
                            <td>@number($detail->qty * $detail->price)</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5">{{ __('Total') }}</th>
                <th>@number(
                    $materialIns->reduce(function ($carry, $item) {
                        return $carry +
                            $item->details->reduce(function ($carry, $item) {
                                return $carry + $item->qty * $item->price;
                            });
                    })
                )</th>
                <th></th>
            </tr>
        </tfoot>
    @endif

</table>
