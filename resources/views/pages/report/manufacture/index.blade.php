@extends('layouts.main')

@section('title', __('Material'))

@section('main-content')
    <div class="section-body">
        <ul id="pageTab" class="nav nav-pills nav-fill" role="tablist">
            <li class="nav-item">
                <a class="nav-link py-1" id="in-tab" data-toggle="tab" href="#in" role="tab"
                    aria-controls="{{ __('in') }}" aria-selected="false"><i class="fas fa-arrow-down"></i>
                    {{ __('Product In') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1" id="out-tab" data-toggle="tab" href="#out" role="tab"
                    aria-controls="{{ __('out') }}" aria-selected="false"><i class="fas fa-arrow-down"></i>
                    {{ __('Material Out') }}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="in" role="tabpanel" aria-labelledby="in-tab">
                @include('pages.report.manufacture._in')
            </div>
            <div class="tab-pane fade" id="out" role="tabpanel" aria-labelledby="out-tab">
                @include('pages.report.manufacture._out')
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.onhashchange = function () {
            const activeTab = location.hash.replace(/^#/, '') || 'in'
            $(`#pageTab a[href="#${activeTab}"].nav-link`).tab('show')
        }

        window.onhashchange()
        
        $('#pageTab a.nav-link').on('click', function(e) {
            window.history.pushState(null, null, `manufactures${e.target.hash}`)
        })
    </script>
@endpush
