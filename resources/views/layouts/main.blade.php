<!DOCTYPE html>

@php $user = Auth::user() @endphp

<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('assets/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <title>
        @hasSection('title')
            @yield('title') &mdash;
        @endif
        {{ config('app.name') }}
    </title>

    @if (App::environment('production'))
        <script src="https://js.sentry-cdn.com/9d286c0b3d5f47ed957410711969c083.min.js" crossorigin="anonymous"></script>
    @endif

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
        integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous" />

    <!-- CSS Libraries -->
    @stack('css-lib')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">


    <!-- CSS Page -->
    @stack('css')
</head>

<body>
    <div id="app">
        @switch($layout ?? null)
            @case('auth')
                @include('layouts._auth')
            @break

            @default
                @include('layouts._with-sidebar')
        @endswitch
    </div>

    <x-_modal id="profile" centered title="{{ __('Edit profile') }}">
        <form action="{{ route('user.update') }}" method="post" id="userProfileForm">
            @csrf

            <div class="form-group">
                <label for="emailInput">{{ __('validation.attributes.email') }}</label>
                <input type="email" class="form-control" name="email" value="{{ $user->email ?? '' }}" required>
            </div>

            <div class="form-group">
                <label for="nameInput">{{ __('validation.attributes.name') }}</label>
                <input type="text" class="form-control" name="name" required value="{{ $user->name ?? '' }}">
            </div>
        </form>

        @slot('footer')
            <button type="submit" form="userProfileForm" class="btn btn-primary">{{ __('Save') }}</button>
        @endslot
    </x-_modal>

    <x-_modal id="change-password" centered title="{{ __('change password') }}" color="warning">
        <form action="{{ route('user.update-password') }}" method="post" id="{{ $uniqid = uniqid() }}">
            @csrf

            <div class="form-group">
                <label class="text-capitalize" for="pwInput">{{ __('validation.attributes.current_password') }}</label>
                <input type="password" class="form-control" name="current_password" minlength="8" maxlength="255" required>
            </div>

            <hr>

            <div class="form-group">
                <label class="text-capitalize" for="pwInput">{{ __('validation.attributes.new_password') }}</label>
                <input type="password" class="form-control" name="new_password" minlength="8" maxlength="255" required>
            </div>

            <div class="form-group">
                <label class="text-capitalize" for="pwInput2">{{ __('validation.attributes.new_password_confirmation') }}</label>
                <input type="password" class="form-control" name="new_password_confirmation" minlength="8"
                    maxlength="255" required>
            </div>

        </form>

        @slot('footer')
            <button type="submit" form="{{ $uniqid }}" class="btn btn-warning">{{ __('Save') }}</button>
        @endslot
    </x-_modal>

    @stack('modal')

    <script>
        window.innerWidth <= 1024 ? window.document.body.classList.add('sidebar-gone') : false
    </script>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"
        integrity="sha512-zMfrMAZYAlNClPKjN+JMuslK/B6sPM09BGvrWlW+cymmPmsUT1xJF3P4kxI3lOh9zypakSgWaTpY6vDJY/3Dig=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"
        integrity="sha512-hlLgIh4nncb2yc4YPtWk5wOykcFxF0fBd5rHfJ6xsALI2khY3H8LbivswJE5Fpz7hws7CJCqOzdyjWHiKJYl+A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
    @stack('js-lib')

    <!-- Template JS File -->
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Page Specific JS File -->
    @stack('js')
</body>

</html>
