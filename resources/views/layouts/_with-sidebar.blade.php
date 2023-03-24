<div class="main-wrapper">
    <div class="navbar-bg"></div>
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="form-inline mr-auto">
            <ul class="navbar-nav mr-3 align-items-center">
                <li>
                    <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="text-job text-white text-monospace" style="line-height: 1.3em; font-size: 0.9em;">
                    <div id="currentTime" class="mt-2" style="font-size: 1.4em"></div>
                    <small id="currentDate"></small>
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
                    {{-- <div class="dropdown-title">Logged in 5 min ago</div> --}}
                    <a href="#profile" data-toggle="modal" class="dropdown-item has-icon text-capitalize">
                        <i class="fas fa-cog"></i> {{ __('profile') }}
                    </a>

                    <a href="#change-password" data-toggle="modal" class="dropdown-item has-icon text-capitalize">
                        <i class="fas fa-lock"></i> {{ __('change password') }}
                    </a>

                    {{-- <a href="features-activities.html" class="dropdown-item has-icon">
                        <i class="fas fa-bolt"></i> Activities
                    </a>
                    <a href="features-settings.html" class="dropdown-item has-icon">
                        <i class="fas fa-cog"></i> Settings
                    </a> --}}

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
                    <i class="fas fa-bolt text-warning"></i>
                    {{ config('app.name') }}
                </a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="{{ route('/') }}">
                    <i class="fas fa-bolt text-warning"></i>
                </a>
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

            @include('layouts.components._alert-catch')

            @yield('main-content')
        </div>
    </div>

    <footer class="main-footer">
        <div class="footer-left">
            <a href="{{ config('app.owner_url') }}" target="_blank">{{ config('app.owner') }}</a>
            <div class="bullet"></div> Copyright &copy; 2023
        </div>
        <div class="footer-right">
            v{{ config('app.version') }}
        </div>
    </footer>
</div>

@once
    @push('js-lib')
        <script>
            moment.locale('{{ app()->getLocale() }}')
        </script>
    @endpush
@endonce

@push('js')
    <script>
        {
            const serverMoment = moment.unix({{ time() }});
            const currentTime = document.getElementById('currentTime');
            const currentDate = document.getElementById('currentDate');

            setInterval(() => {
                serverMoment.add(1, 'second');
                currentTime.innerText = serverMoment.format('HH:mm:ss');
                currentDate.innerText = serverMoment.format('DD-MM-YYYY');
            }, 1000);
        };
    </script>
@endpush
