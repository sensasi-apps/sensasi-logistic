<table class="table table-striped table-bordered" style="width:100%">
    <thead class="text-capitalize">
        <tr>
            <th>#</th>
            <th>{{ __('code') }}</th>
            <th>{{ __('at') }}</th>
            <th>{{ __('type') }}</th>
            <th>{{ __('material') }}</th>
            <th>{{ __('qty') }}</th>
            <th>{{ __('price') }}</th>
            <th>{{ __('subtotal') }}</th>
        </tr>
    </thead>

    @if ($materialIns->count() == 0)

        <tbody>
            <tr>
                <td colspan="8" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($materialIns as $materialIn)
                @foreach ($materialIn->details as $detail)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $materialIn->details->count() }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $materialIn->details->count() }}">{{ $materialIn->code }} </td>
                            <td rowspan="{{ $materialIn->details->count() }}">
                                {{ date_format($materialIn->at, 'd-m-Y') }}
                            </td>
                            <td rowspan="{{ $materialIn->details->count() }}">{{ $materialIn->type }}</td>
                        @endif

                        <td>{{ $detail->material->id_for_human }}</td>

                        <td>@number($detail->qty) {{ $detail->material->unit }}</td>
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
                    $materialIns->reduce(function ($carry, $item) {
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
