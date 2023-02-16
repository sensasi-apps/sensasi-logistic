@include('components.assets._alpinejs')
@include('components.assets._datatable')

@once
    @push('js-lib')
        <script src="{{ asset('assets/js/alpine-components/datatable.js') }}"></script>
    @endpush
@endonce
