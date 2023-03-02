<table class="table table-striped table-bordered" style="width:100%">
    <thead class="text-capitalize">
        <tr>
            <th>#</th>
            <th>{{ __('code') }}</th>
            <th>{{ __('brand') }}</th>
            <th>{{ __('name') }}</th>
            <th>{{ __('qty') }}</th>
            <th>{{ __('price') }}</th>
            <th>{{ __('subtotal') }}</th>
        </tr>
    </thead>

    @php
        $total = 0;
    @endphp

    @if ($productOutDetailsGroupByProduct->count() == 0)
        <tbody>
            <tr>
                <td colspan="7" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($productOutDetailsGroupByProduct as $productOutDetails)
                @php
                    $subtotal = $productOutDetails->reduce(function ($carry, $productOutDetail) {
                        return $carry + $productOutDetail->qty * $productOutDetail->productInDetail->price;
                    });
                    
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $productOutDetails->first()->productInDetail->product->code }}</td>
                    <td>{{ $productOutDetails->first()->productInDetail->product->brand }}</td>
                    <td>{{ $productOutDetails->first()->productInDetail->product->name }}</td>
                    <td>@number($productOutDetails->sum('qty')) {{ $productOutDetails->first()->productInDetail->product->unit }}</td>
                    <td>{{ __('$') }} @number($subtotal / $productOutDetails->sum('qty'))</td>
                    <td>{{ __('$') }} @number($subtotal)</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-center text-uppercase">{{ __('Total') }}</th>
                <th>{{ __('$') }} @number($total)</th>
            </tr>
        </tfoot>
    @endif

</table>
