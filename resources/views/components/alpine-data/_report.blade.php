@include('components.assets._alpinejs')
@include('components.assets._datepicker')

@once
    @push('js-lib')
        <script src="{{ asset('assets/js/alpine-components/report.js') }}"></script>
    @endpush
@endonce
