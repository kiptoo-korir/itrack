<header id="header" class="container-fluid">
    <nav class="navbar navbar-light bg-white navbar-expand-md px-3 row shadow-sm">
        <a class="navbar-brand d-none d-sm-block" href="{{ route('home') }}">
            <img src="{{ asset('img/2.png') }}" height="25px"
                style="border-radius: 4px; box-shadow: 2px 2px 2px black;" alt="">
        </a>
        <button class="navbar-toggler mr-auto ml-2" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapse and left most --}}
        <div class="collapse navbar-collapse justify-content-center order-last" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">

            </ul>
            <!-- Right Side Of Collapse -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link text-black" href="{{ route('login') }}">{{ __('Sign In') }}</a>
                    </li>
                @endif

                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link text-black" href="{{ route('register') }}">{{ __('Sign Up') }}</a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link text-black" href="{{ route('about') }}">{{ __('About') }}</a>
                </li>
            </ul>
        </div>
    </nav>
</header>
