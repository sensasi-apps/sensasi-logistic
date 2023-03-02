<table class="table table-striped table-bordered" style="width:100%">
    <thead class="text-capitalize">
        <tr>
            <th>#</th>
            <th>{{ __('code') }}</th>
            <th>{{ __('at') }}</th>
            <th>{{ __('type') }}</th>
            <th>{{ __('product') }}</th>
            <th>{{ __('qty') }}</th>
            <th>{{ __('price') }}</th>
            <th>{{ __('subtotal') }}</th>
        </tr>
    </thead>

    @if ($productIns->count() == 0)

        <tbody>
            <tr>
                <td colspan="8" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($productIns as $productIn)
                @foreach ($productIn->details as $detail)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $productIn->details->count() }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $productIn->details->count() }}">{{ $productIn->code }} </td>
                            <td rowspan="{{ $productIn->details->count() }}">
                                {{ date_format($productIn->at, 'd-m-Y') }}
                            </td>
                            <td rowspan="{{ $productIn->details->count() }}">{{ $productIn->type }}</td>
                        @endif

                        <td>{{ $detail->product->id_for_human }}</td>

                        <td>@number($detail->qty) {{ $detail->product->unit }}</td>
                        <td>{{ __('$') }} @number($detail->price)</td>
                        <td>{{ __('$') }} @number($detail->qty * $detail->price)</td>

                    </tr>
                @endforeach
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="7" class="text-center text-uppercase">{{ __('Total') }}</th>
                <th>{{ __('$') }} @number(
                    $productIns->reduce(function ($carry, $item) {
                        return $carry +
                            $item->details->reduce(function ($carry, $item) {
                                return $carry + $item->qty * $item->price;
                            });
                    })
                )</th>
            </tr>
        </tfoot>
    @endif

</table>
