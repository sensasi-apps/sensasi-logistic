<div class="main-wrapper">
    <div class="navbar-bg"></div>
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="form-inline mr-auto">
            <ul class="navbar-nav mr-3">
                <li>
                    <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
        </div>

        <ul class="navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">{{ Auth::user()->name }}</div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">Logged in 5 min ago</div>
                    <a href="features-profile.html" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profile
                    </a>
                    <a href="features-activities.html" class="dropdown-item has-icon">
                        <i class="fas fa-bolt"></i> Activities
                    </a>
                    <a href="features-settings.html" class="dropdown-item has-icon">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('auth.logout') }}" method="POST" id="logoutForm">
                        @csrf
                    </form>
                    <button type="submit" form="logoutForm" class="dropdown-item has-icon text-danger btn">
                        <span class="fas fa-sign-out-alt" style="margin: 1px 10px 0px 4px"></span>Logout
                    </button>
                </div>
            </li>
        </ul>
    </nav>
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="{{ route('/') }}">
                    <i class="fas fa-box"></i>
                    {{ config('app.name') }}
                </a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="{{ route('/') }}">
                    <i class="fas fa-box"></i>
                </a>
            </div>

            <div class="text-right pr-4" style="transform: translateY(-1em)">
                <span id="currentDate"></span>
            </div>

            @include('layouts.components._sidebar-menu-ul')
        </aside>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="section">
            @hasSection('title')
                <div class="section-header">
                    <h1>@yield('title')</h1>
                </div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    @include('components._alert', ['message' => $error])
                @endforeach
            @endif

            @yield('main-content')
        </div>
    </div>

    <footer class="main-footer">
        <div class="footer-left">
            <a href="{{ config('app.owner_url') }}" target="_blank">{{ config('app.owner') }}</a>
            <div class="bullet"></div> Copyright &copy; 2022
        </div>
        {{-- <div class="footer-right">
            v12.34
        </div> --}}
    </footer>
</div>

@once
    @push('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"
            integrity="sha512-hlLgIh4nncb2yc4YPtWk5wOykcFxF0fBd5rHfJ6xsALI2khY3H8LbivswJE5Fpz7hws7CJCqOzdyjWHiKJYl+A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            moment.locale('{{ config('app.locale') }}')
        </script>
    @endpush
@endonce

@push('js')
    <script>
        {
            const currentDateVal = moment().format('L');
            $('#currentDate').html(currentDateVal);
        };
    </script>
@endpush
