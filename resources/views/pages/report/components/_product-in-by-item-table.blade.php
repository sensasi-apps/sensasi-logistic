<table class="table table-striped table-bordered" style="width:100%">
    <thead class="text-capitalize">
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">{{ __('code') }}</th>
            <th rowspan="2">{{ __('brand') }}</th>
            <th rowspan="2">{{ __('name') }}</th>
            <th rowspan="2">{{ __('qty') }}</th>
            <th colspan="2">{{ __('cost') }}</th>
            <th colspan="2">{{ __('value') }}</th>

        </tr>

        <tr>
            <th>@ {{ __('unit') }}</th>
            <th>{{ __('subtotal') }}</th>
            <th>@ {{ __('unit') }}</th>
            <th>
                <span data-toggle="tooltip" data-placement="top" title="Estimasi nilai dasar">
                    {{ __('subtotal') }}
                    <span class="text-danger">*</span>
                </span>
            </th>

        </tr>
    </thead>

    @php
        $total_cost = 0;
        $total_value = 0;
    @endphp

    @if ($productInDetailsGroupByProduct->count() == 0)
        <tbody>
            <tr>
                <td colspan="7" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($productInDetailsGroupByProduct as $productInDetails)
                @php
                    $subtotal_cost = $productInDetails->reduce(function ($carry, $productInDetail) {
                        return $carry + $productInDetail->qty * $productInDetail->price;
                    });
                    
                    $subtotal_value = $productInDetails->reduce(function ($carry, $productInDetail) {
                        return $carry + $productInDetail->qty * $productInDetail->product->default_price;
                    });
                    
                    $total_cost += $subtotal_cost;
                    $total_value += $subtotal_value;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $productInDetails->first()->product->code }}</td>
                    <td>{{ $productInDetails->first()->product->brand }}</td>
                    <td>{{ $productInDetails->first()->product->name }}</td>
                    <td>@number($productInDetails->sum('qty')) {{ $productInDetails->first()->product->unit }}</td>
                    <td>{{ __('$') }} @number($subtotal_cost / $productInDetails->sum('qty'))</td>
                    <td>{{ __('$') }} @number($subtotal_cost)</td>
                    <td>{{ __('$') }} @number($subtotal_value / $productInDetails->sum('qty'))</td>
                    <td>{{ __('$') }} @number($subtotal_value)</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-center text-uppercase">{{ __('Total') }}</th>
                <th>{{ __('$') }} @number($total_cost)</th>
                <th></th>
                <th>{{ __('$') }} @number($total_value)</th>
            </tr>
        </tfoot>
    @endif

</table>
