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
    {{-- <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet"> --}}
    <style>
        .avatar-text {
            height: 40px;
            width: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #e9ecef;
            background-color: #075db8;
            font-size: 20px;
        }

        .avatar {
            height: 40px;
            width: 40px;
        }

        #app {
            min-height: 100vh;
        }

    </style>
    @yield('css_scripts')
</head>

<body>
    <div id="app" class="bg-light">
        @include('components.navbar')

        <main class="py-4">
            @includeWhen(!$isTokenValid, 'components.no-token-cta')
            @include('components.spinner')
            @yield('content')
            @include('components.toasts')
            @yield('modals')
        </main>
    </div>
</body>
<script src="{{ asset('js/jquery-3.6.0.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/app_echo.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>
<script>
    const userId = {{ $user_data->id }};
    const notification_count = {{ $notification_count }};
    const notificationsRoute = "{{ route('fetch_notifications') }}";
    const markAsReadUrl = "{{ route('mark_as_read', '') }}";
</script>
@yield('js_scripts')
<script src="{{ asset('js/add_notification.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('notification-dropdown').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>

</html>
