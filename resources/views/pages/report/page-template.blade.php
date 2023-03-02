@extends('layouts.main')

@section('title', $title)

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">
@endpush

@section('main-content')
    <div class="section-body">
        <ul class="nav nav-pills nav-fill" role="tablist">
            @foreach ($tabs as $key => $tab)
                <li class="nav-item">
                    <a class="nav-link py-1" id="{{ $key }}-tab" data-toggle="tab" href="#{{ $key }}"
                        role="tab" aria-controls="$key" aria-selected="false">
                        <i class="fas fa-arrow-{{ str_contains($key, 'in') ? 'down' : 'up' }}"></i>
                        {{ $tab }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach ($tabs as $key => $tab)
                <div class="tab-pane fade" id="{{ $key }}" role="tabpanel"
                    aria-labelledby="{{ $key }}-tab">
                    @include('pages.report.components._sub-tab-page')
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('js')
    <script>
        tab_system_init('{{ $reportPageId }}s', 'in');
    </script>
@endpush
