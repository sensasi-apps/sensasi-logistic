@extends('layouts.main', ['layout' => 'auth'])

@section('title', __('auth.Forgot Password'))

@section('main-content')
    <form method="POST" action="{{ action('App\Http\Controllers\AuthController@forgotPassword') }}" class="needs-validation" novalidate="">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" value="{{ old('email') }}" type="email" class="form-control @error('email') is-invalid @enderror" name="email" tabindex="1" required autofocus>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                {{__('Reset Password')}}
            </button>
        </div>
    </form>

@endsection
