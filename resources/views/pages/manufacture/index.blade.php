@extends('layouts.main')

@section('title', ucfirst(__('manufacture')))

@section('main-content')
    <div class="section-body">
        @include('pages.manufacture._manufactures')
    </div>
@endsection
