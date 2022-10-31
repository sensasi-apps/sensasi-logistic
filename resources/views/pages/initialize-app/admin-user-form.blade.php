@extends('layouts.auth')

@section('page-title', 'Daftarkan Super Admin')


@push('js-lib')
    <script src="{{ asset('assets/modules/jquery-pwstrength/jquery.pwstrength.min.js') }}"></script>
@endpush

@push('js')
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                $('#userFormModal').modal('show')
            });
        </script>
    @endif
@endpush

@push('js-page')
    <script src="{{ asset('assets/js/page/auth-register.js') }}"></script>

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

                    <form action="{{ route('initialize-app.store-admin-user') }}" method="POST" id="userForm">
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
@endpush

@section('page-body')

	<div class="d-flex justify-content-center">
		<button type="button" class="btn btn-outline-primary mr-2 btn-lg" data-toggle="modal" data-target="#userFormModal">
			Daftar sekarang
		</button>
		
		<a class="btn btn-outline-primary btn-lg" href="{{ route('initialize-app.sign-up-admin-with-google') }}">
			<span class="fab fa-google"></span> Daftar dengan Google
		</a>
	</div>

@endsection
