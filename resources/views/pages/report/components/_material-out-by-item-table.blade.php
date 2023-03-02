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

    @if ($materialOutDetailsGroupByMaterial->count() == 0)
        <tbody>
            <tr>
                <td colspan="7" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($materialOutDetailsGroupByMaterial as $materialOutDetails)
                @php
                    $subtotal = $materialOutDetails->reduce(function ($carry, $materialOutDetail) {
                        return $carry + $materialOutDetail->qty * $materialOutDetail->materialInDetail->price;
                    });
                    
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $materialOutDetails->first()->materialInDetail->material->code }}</td>
                    <td>{{ $materialOutDetails->first()->materialInDetail->material->brand }}</td>
                    <td>{{ $materialOutDetails->first()->materialInDetail->material->name }}</td>
                    <td>@number($materialOutDetails->sum('qty')) {{ $materialOutDetails->first()->materialInDetail->material->unit }}</td>
                    <td>{{ __('$') }} @number($subtotal / $materialOutDetails->sum('qty'))</td>
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
