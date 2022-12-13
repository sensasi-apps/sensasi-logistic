@extends('layouts.main')

@section('title', __('Manufacture'))

@section('main-content')
    <div class="section-body">
        @include('pages.manufacture._manufactures')
    </div>
@endsection
