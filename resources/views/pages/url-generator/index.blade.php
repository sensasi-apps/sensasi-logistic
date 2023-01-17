@extends('layouts.main')

@section('title', __('Dashboard'))

@include('components.assets._datatable')
@include('components.assets._select2')
@include('components.assets._qrcode')

@section('main-content')
    <div class="section-body">
        <h2 class="section-title">
            {{ __(Auth::user()->name) }}
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="qrcode"></div>
                    <a href="{!!URL::temporarySignedRoute('url_generator.index', now()->addDays(1), ['data' => Auth::user()->id])!!}">{!!URL::temporarySignedRoute("url_generator.index", now()->addHours(1), ["data" => Auth::user()->id])!!}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        // new QRCode(document.getElementById("qrcode"), "anu.asd");

        new QRCode("qrcode", {
            text: `{!!URL::temporarySignedRoute("url_generator.index", now()->addDays(1), ["data" => Auth::user()->id])!!}`,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
@endpush
