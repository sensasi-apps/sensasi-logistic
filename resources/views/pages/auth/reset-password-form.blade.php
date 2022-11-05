@extends('layouts.main', ['layout' => 'auth'])

@section('title', __('Reset password'))

@section('main-content')
    <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate="">
        @csrf

        <input type="hidden" name="token" value={{ $token }} required>
        <input type="hidden" name="email" value={{ $email }} required>

        <div class="form-group">
            <label for="password">{{ __('New password') }}</label>
            <input id="password" type="password" class="form-control" name="password" required autofocus>
        </div>

        <div class="form-group">
            <label for="password_confirmation">{{ __('Confirm password') }}</label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success btn-lg btn-block">
                {{ __('Save') }}
            </button>
        </div>
    </form>
@endsection
