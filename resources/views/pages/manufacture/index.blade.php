@extends('layouts.main')

@section('title', __('Material'))

@section('main-content')
    <div class="section-body">
        <ul class="nav nav-pills nav-fill" id="myTab4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active py-1" id="list-tab" data-toggle="tab" href="#list" role="tab"
                    aria-controls="{{ __('list') }}" aria-selected="true"><i class="fas fa-pallet"></i>
                    {{ __('Material List') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  py-1" id="in-tab" data-toggle="tab" href="#in" role="tab"
                    aria-controls="{{ __('in') }}" aria-selected="false"><i class="fas fa-boxes"></i>
                    {{ __('Manufacture List') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  py-1" id="out-tab" data-toggle="tab" href="#out" role="tab"
                    aria-controls="{{ __('out') }}" aria-selected="false"><i class="fas fa-box"></i>
                    {{ __('product List') }}</a>
            </li>
        </ul>

        <div class="tab-content" id="myTab2Content">
            <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
                @include('pages.materials._material-list')
            </div>
            <div class="tab-pane fade show active" id="in" role="tabpanel" aria-labelledby="in-tab">
                @include('pages.manufacture._manufactures')
            </div>
            <div class="tab-pane fade" id="out" role="tabpanel" aria-labelledby="out-tab">
                @include('pages.products._product-list')
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Javascript to enable link to tab
        var hash = location.hash.replace(/^#/, ''); // ^ means starting, meaning only match the first hash

        if (hash) {
            $(`a[href="#${hash}"].nav-link`).tab('show');
        }

        // Change hash for page-reload
        $('.nav-link').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash
        })
    </script>
@endpush
