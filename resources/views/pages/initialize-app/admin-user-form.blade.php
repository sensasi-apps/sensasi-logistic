@extends('layouts.main', ['layout' => 'auth'])

@section('title', 'Daftarkan Super Admin')


@push('js')
    <div id="userFormModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Silahkan mengisi data Super Admin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            @include('components._alert', ['message' => $error])
                        @endforeach
                    @endif

                    <form action="{{ route('initialize-app.create-admin-user.store') }}" method="POST" id="userForm">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" value="{{ old('name') }}" type="text" required
                                class="form-control @error('name') is-invalid @enderror" name="name" autofocus>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" required value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="password" class="d-block">Password</label>
                                <input id="password" type="password"
                                    class="form-control pwstrength @error('password') is-invalid @enderror"
                                    data-indicator="pwindicator" name="password" required>
                                <div id="pwindicator" class="pwindicator">
                                    <div class="bar"></div>
                                    <div class="label"></div>
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label for="password_confirmation" class="d-block">Password Confirmation</label>
                                <input {{ old('name') }} id="password_confirmation" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password_confirmation" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" form="userForm" class="btn btn-primary">Daftarkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/page/auth-register.js') }}"></script>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                $('#userFormModal').modal('show')
            });
        </script>
    @endif

@endpush

@section('main-content')

    <button type="button" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal"
        data-target="#userFormModal">
        Daftar sekarang
    </button>

    <a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('initialize-app.create-admin-user.oauth.google') }}">
        <span class="fab fa-google"></span> Daftar dengan Google
    </a>

@endsection
