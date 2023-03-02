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

    @if ($materialInDetailsGroupByMaterial->count() == 0)
        <tbody>
            <tr>
                <td colspan="7" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($materialInDetailsGroupByMaterial as $materialInDetails)
                @php
                    $subtotal = $materialInDetails->reduce(function ($carry, $item) {
                        return $carry + $item->qty * $item->price;
                    });
                    
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $materialInDetails->first()->material->code }}</td>
                    <td>{{ $materialInDetails->first()->material->brand }}</td>
                    <td>{{ $materialInDetails->first()->material->name }}</td>
                    <td>@number($materialInDetails->sum('qty')) {{ $materialInDetails->first()->material->unit }}</td>
                    <td>{{ __('$') }} @number($subtotal / $materialInDetails->sum('qty'))</td>
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
