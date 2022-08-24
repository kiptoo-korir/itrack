<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->

    {{-- favicon --}}
    <link rel="shortcut icon" href="{{ asset('img/itrack_icon_2.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/toast_spinner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simple_toast.min.css') }}">

    @yield('css_scripts')

    <style>
        #app {
            min-height: 100vh;
        }

        .text-black {
            color: black !important;
        }
    </style>
</head>

<body>
    <div id="app" class="bg-light">
        @include('components.navbar-unauthenticated')
        <main class="py-4">
            @include('components.spinner')
            @yield('content')
            @yield('modals')
        </main>
    </div>
</body>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/simple_toast.min.js') }}"></script>
<script>
    const simpleToast = new SimpleToast({
        duration: 6000,
        position: "top-right"
    });
</script>
@yield('js_scripts')

</html>
