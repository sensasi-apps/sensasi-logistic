<table class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Quantity') }}</th>
            <th>{{ __('Price') }} ({{ __('$') }})</th>
            <th>{{ __('Subtotal') }} ({{ __('$') }})</th>
        </tr>
    </thead>

    @php
        $total = 0;
    @endphp

    @if ($materialsInGroup->count() == 0)
        <tbody>
            <tr>
                <td colspan="4" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($materialsInGroup as $materialInDetails)
                @php
                    $subtotal = $materialInDetails->reduce(function ($carry, $item) {
                        return $carry + $item->qty * $item->price;
                    });
                    
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>
                        {{ $materialInDetails->first()->material->name }}
                        {{ $materialInDetails->first()->material->brand }}
                    </td>
                    <td>@number($materialInDetails->sum('qty')) {{ $materialInDetails->first()->material->unit }}</td>
                    <td>@number($subtotal / $materialInDetails->sum('qty'))</td>
                    <td>@number($subtotal)</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">{{ __('Total') }}</th>
                <th>@number($total)</th>
            </tr>
        </tfoot>
    @endif

</table>
