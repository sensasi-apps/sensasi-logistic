<table class="table table-striped table-bordered" style="width:100%">
    <thead class="text-capitalize">
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">{{ __('code') }}</th>
            <th rowspan="2">{{ __('at') }}</th>
            <th colspan="2">{{ __('material out') }}</th>
            <th colspan="2">{{ __('material in') }}</th>
        </tr>
        <tr>
            <th>{{ __('name') }}</th>
            <th>{{ __('subtotal') }}</th>
            <th>{{ __('name') }}</th>
            <th>{{ __('subtotal') }}</th>
        </tr>
    </thead>

    @if ($materialManufactures->count() == 0)

        <tbody>
            <tr>
                <td colspan="8" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($materialManufactures as $materialManufacture)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $materialManufacture->code }}</td>
                    <td>{{ $materialManufacture->at->format('d-m-Y') }}</td>

                    <td>
                        @foreach ($materialManufacture->materialOut->details as $detail)
                            <div>
                                {{ $detail->materialInDetail->material->id_for_human }} &times;
                                {{ $detail->qty }}
                                {{ $detail->materialInDetail->material->unit }}
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($materialManufacture->materialOut->details as $detail)
                            <div>
                                {{ __('$') }} @number($detail->qty * $detail->materialInDetail->price)
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($materialManufacture->materialIn->details as $detail)
                            <div>
                                {{ $detail->material->id_for_human }} &times; {{ $detail->qty }}
                                {{ $detail->material->unit }}
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($materialManufacture->materialIn->details as $detail)
                            <div>
                                {{ __('$') }} @number($detail->qty * $detail->price)
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4" class="text-center text-uppercase">{{ __('Total') }}</th>
                <th>{{ __('$') }} @number(
                    $materialManufactures->reduce(function ($carry, $materialManufacture) {
                        return $carry +
                            $materialManufacture->materialOut->details->reduce(function ($carry, $detail) {
                                return $carry + $detail->qty * $detail->materialInDetail->price;
                            });
                    })
                )</th>
                <th></th>
                <th>{{ __('$') }} @number(
                    $materialManufactures->reduce(function ($carry, $materialManufacture) {
                        return $carry +
                            $materialManufacture->materialIn->details->reduce(function ($carry, $detail) {
                                return $carry + $detail->qty * $detail->price;
                            });
                    })
                )</th>
            </tr>
        </tfoot>
    @endif

</table>
