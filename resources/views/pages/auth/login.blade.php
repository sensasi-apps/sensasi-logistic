@extends('layouts.main', ['layout' => 'auth'])

@section('title', 'Silahkan masuk')

@section('main-content')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            @include('components._alert', ['message' => $error])
        @endforeach
    @endif

    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" value="{{ old('email') }}" type="email" class="form-control @error('email') is-invalid @enderror" name="email" tabindex="1" required autofocus>
        </div>

        <div class="form-group">
            <div class="d-block">
                <label for="password" class="control-label">Password</label>
                {{-- <div class="float-right">
                    <a href="auth-forgot-password.html" class="text-small">
                        {{ __('auth.Forgot Password') }} ?
                    </a>
                </div> --}}
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" tabindex="2" required>
        </div>

        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                <label class="custom-control-label" for="remember-me">Remember Me</label>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                Masuk
            </button>
        </div>
    </form>
    <div class="text-center mt-4 mb-3">
        <div class="text-job text-muted">{{ __('auth.Login With Social') }}</div>
    </div>
    <div class="row sm-gutters">
        <a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('login.oauth.google') }}">
            <span class="fab fa-google"></span> Akun Google
        </a>

    </div>

@endsection
