@include('components.assets._alpinejs')

@once
    @push('js-lib')
        <script src="{{ asset('assets/js/alpine-components/crud.js') }}"></script>
    @endpush
@endonce
