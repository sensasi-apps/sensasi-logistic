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

    @if ($materialOuts->count() == 0)

        <tbody>
            <tr>
                <td colspan="8" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($materialOuts as $materialOut)
                @foreach ($materialOut->details as $detail)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $materialOut->details->count() }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $materialOut->details->count() }}">{{ $materialOut->code }} </td>
                            <td rowspan="{{ $materialOut->details->count() }}">
                                {{ date_format($materialOut->at, 'd-m-Y') }}
                            </td>
                            <td rowspan="{{ $materialOut->details->count() }}">{{ $materialOut->type }}</td>
                        @endif

                        <td>{{ $detail->materialInDetail->material->id_for_human }}</td>

                        <td>@number($detail->qty) {{ $detail->materialInDetail->material->unit }}</td>
                        <td>{{ __('$') }} @number($detail->materialInDetail->price)</td>
                        <td>{{ __('$') }} @number($detail->qty * $detail->materialInDetail->price)</td>

                    </tr>
                @endforeach
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="7" class="text-center text-uppercase">{{ __('Total') }}</th>
                <th>{{ __('$') }} @number(
                    $materialOuts->reduce(function ($carry, $materialOut) {
                        return $carry +
                            $materialOut->details->reduce(function ($carry, $detail) {
                                return $carry + $detail->qty * $detail->materialInDetail->price;
                            });
                    })
                )</th>
            </tr>
        </tfoot>
    @endif

</table>
