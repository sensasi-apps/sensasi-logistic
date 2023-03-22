<table class="table table-striped table-bordered" style="width:100%">
    <thead class="text-capitalize">
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">{{ __('code') }}</th>
            <th rowspan="2">{{ __('at') }}</th>
            <th colspan="2">{{ __('material out') }}</th>
            <th colspan="2">{{ __('product in') }}</th>
        </tr>
        <tr>
            <th>{{ __('name') }}</th>
            <th>{{ __('subtotal') }}</th>
            <th>{{ __('name') }}</th>
            <th>
                <span data-toggle="tooltip" data-placement="top" title="Estimasi nilai dasar">
                    {{ __('subtotal') }}
                    <span class="text-danger">*</span>
                </span>
            </th>
        </tr>
    </thead>

    @if ($productManufactures->count() == 0)

        <tbody>
            <tr>
                <td colspan="8" class="text-center">{{ __('No data available in table') }}</td>
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach ($productManufactures as $productManufacture)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $productManufacture->code }}</td>
                    <td>{{ $productManufacture->at->format('d-m-Y') }}</td>

                    <td>
                        @foreach ($productManufacture->materialOut->details as $detail)
                            <div>
                                {{ $detail->materialInDetail->material->id_for_human }} &times;
                                {{ $detail->qty }}
                                {{ $detail->materialInDetail->material->unit }}
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($productManufacture->materialOut->details as $detail)
                            <div>
                                {{ __('$') }} @number($detail->qty * $detail->materialInDetail->price)
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($productManufacture->productIn->details as $detail)
                            <div>
                                {{ $detail->product->id_for_human }} &times; {{ $detail->qty }}
                                {{ $detail->product->unit }}
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($productManufacture->productIn->details as $detail)
                            <div>
                                {{ __('$') }} @number($detail->qty * $detail->product->default_price)
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
                    $productManufactures->reduce(function ($carry, $productManufacture) {
                        return $carry +
                            $productManufacture->materialOut->details->reduce(function ($carry, $detail) {
                                return $carry + $detail->qty * $detail->materialInDetail->price;
                            });
                    })
                )</th>
                <th></th>
                <th>{{ __('$') }} @number(
                    $productManufactures->reduce(function ($carry, $productManufacture) {
                        return $carry +
                            $productManufacture->productIn->details->reduce(function ($carry, $detail) {
                                return $carry + $detail->qty * $detail->product->default_price;
                            });
                    })
                )</th>
            </tr>
        </tfoot>
    @endif

</table>
