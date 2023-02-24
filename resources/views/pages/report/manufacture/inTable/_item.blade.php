<table class="table table-striped table-bordered" style="width:100%">
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Quantity') }}</th>
    </tr>

    @foreach ($productInDetailItem as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ $row->qty }} {{ $row->unit }}</td>
        </tr>
    @endforeach
</table>
