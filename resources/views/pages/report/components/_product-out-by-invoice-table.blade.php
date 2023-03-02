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

    @if ($productOuts->count() == 0)

        <tbody>
            <tr>
                <td colspan="8" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($productOuts as $productOut)
                @foreach ($productOut->details as $detail)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $productOut->details->count() }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $productOut->details->count() }}">{{ $productOut->code }} </td>
                            <td rowspan="{{ $productOut->details->count() }}">
                                {{ date_format($productOut->at, 'd-m-Y') }}
                            </td>
                            <td rowspan="{{ $productOut->details->count() }}">{{ $productOut->type }}</td>
                        @endif

                        <td>{{ $detail->productInDetail->product->id_for_human }}</td>

                        <td>@number($detail->qty) {{ $detail->productInDetail->product->unit }}</td>
                        <td>{{ __('$') }} @number($detail->productInDetail->price)</td>
                        <td>{{ __('$') }} @number($detail->qty * $detail->productInDetail->price)</td>

                    </tr>
                @endforeach
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="7" class="text-center text-uppercase">{{ __('Total') }}</th>
                <th>{{ __('$') }} @number(
                    $productOuts->reduce(function ($carry, $productOut) {
                        return $carry +
                            $productOut->details->reduce(function ($carry, $detail) {
                                return $carry + $detail->qty * $detail->productInDetail->price;
                            });
                    })
                )</th>
            </tr>
        </tfoot>
    @endif

</table>
