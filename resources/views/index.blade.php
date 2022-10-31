@extends('layouts.with-sidebar')

@section('page-title', 'Produk')

@section('page-body')
    <div class="section-body">
        <h2 class="section-title">Daftar Produk</h2>
        {{-- <p class="section-lead">This page is just an example for you to create your own page.</p> --}}

        <div class="card">
            {{-- <div class="card-header">
                <h4>Example Card</h4>
            </div> --}}
            <div class="card-body">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
            {{-- <div class="card-footer bg-whitesmoke">
                This is card footer
            </div> --}}
        </div>
    </div>
@endsection
