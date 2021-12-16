<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/fontawesome.min.css"
        integrity="sha384-wESLQ85D6gbsF459vf1CiZ2+rr+CsxRY0RpiF1tLlQpDnAgg6rwdsUF1+Ics2bni" crossorigin="anonymous">

    {{-- favicon --}}
    <link rel="shortcut icon" href="{{ asset('img/itrack_icon_2.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/toast_spinner.css') }}">

    @yield('css_scripts')

    <style>
        #app {
            min-height: 100vh;
        }

        .text-black {
            color: black !important;
        }

        .btn-navy {
            background: #075db8;
            color: #f8f9fa !important;
            border-color: #075db8;
        }

        .btn-navy:hover {
            background: #f8f9fa;
            color: #075db8 !important;
            border-color: #075db8;
        }

        /* @media (max-width: 576px) {
            #navbar-landing {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            }
        } */

    </style>
</head>

<body>
    <div id="app" class="bg-light">
        <header id="header" class="shadow-sm">
            <div class="container">
                <nav id="navbar-landing" class="navbar navbar-light bg-light px-3 row">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('img/2.png') }}" height="25px"
                            style="border-radius: 4px; box-shadow: 2px 2px 2px black;" alt="">
                    </a>
                    <!-- Right Side Of Collapse -->
                    <ul class="ml-auto list-unstyled d-flex flex-row my-auto">
                        <!-- Authentication Links -->
                        @if (Route::has('login'))
                            <li class="nav-item mr-2">
                                <a class="nav-link text-black" href="{{ route('home') }}">{{ __('Sign In') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link btn btn-navy d-inline-block"
                                    href="{{ route('register') }}">{{ __('Sign Up') }}</a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>

        </header>

        <main class="py-4">
            @include('components.spinner')
            @yield('content')
            @include('components.toasts')
            @yield('modals')
        </main>
    </div>
</body>
<script src="{{ asset('js/jquery-3.6.0.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>
@yield('js_scripts')

</html>
