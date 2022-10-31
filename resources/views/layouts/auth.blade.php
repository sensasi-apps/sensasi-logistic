<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	<title>
		@hasSection('page-title')
			@yield('page-title') &mdash;
		@endif
		{{ config('app.name') }}
	</title>


    <!-- General CSS Files -->
	<link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
	@stack('css-lib')

    <!-- Template CSS -->
	<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">

					@if (isset($bodySize) && $bodySize === 'lg')
						<div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
					@else
	                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
					@endif

                        <div class="login-brand">
							{{ config('app.name') }}
                            {{-- <img src="assets/img/stisla-fill.svg" alt="logo" width="100"
                                class="shadow-light rounded-circle"> --}}
								
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>@yield('page-title')</h4>
                            </div>

                            <div class="card-body">
                                @yield('page-body')
                            </div>
                        </div>

                        <div class="simple-footer">
							<a href="{{ config('app.owner_url') }}" target="_blank">{{ config('app.owner') }}</a> <div class="bullet"></div> Copyright &copy; 2022
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
	<script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/popper.js') }}"></script>
    <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
	@stack('js-lib')

    <!-- Page Specific JS File -->
	@stack('js-page')

    <!-- Template JS File -->
	<script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

	@stack('js')

</body>

</html>
