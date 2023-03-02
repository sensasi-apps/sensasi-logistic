<section class="section">
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

                <div class="login-brand">
                    {{ config('app.name') }}
                    {{-- <img src="assets/img/stisla-fill.svg" alt="logo" width="100"
                    class="shadow-light rounded-circle"> --}}
                </div>

                <div class="card card-primary">
                    <div class="card-header">
                        <h4>@yield('title')</h4>
                    </div>

                    <div class="card-body">
                        @include('layouts.components._alert-catch')

                        @yield('main-content')
                    </div>
                </div>

                <div class="simple-footer">
                    <a href="{{ config('app.owner_url') }}" target="_blank">{{ config('app.owner') }}</a>
                    <div class="bullet"></div> Copyright &copy; 2023
                </div>
            </div>
        </div>
    </div>
</section>
