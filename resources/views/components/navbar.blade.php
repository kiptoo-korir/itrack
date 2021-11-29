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
                @auth
                    <li class="nav-item">
                        <a class="nav-link text-dark {{ request()->is('/') || request()->is('project*') ? 'active-link' : '' }}"
                            href="{{ route('home') }}">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark {{ request()->is('repositor*') ? 'active-link' : '' }}"
                            href="{{ route('repositories') }}">Repositories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark {{ request()->is('reminders') ? 'active-link' : '' }}"
                            href="{{ route('reminders_view') }}">Reminders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark {{ request()->is('notes') ? 'active-link' : '' }}"
                            href="{{ route('notes_view') }}">Notes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark {{ request()->is('task') ? 'active-link' : '' }}"
                            href="{{ route('task_view') }}">Task Lists</a>
                    </li>
                @endauth
            </ul>
            <!-- Right Side Of Collapse -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>

        @auth
            <div id="notifications" class="d-flex order-0 order-md-last">
                <div class="dropdown nav-item">
                    <a class="nav-link notification-bell" href="#" id="bell" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                            <div class="media-body">
                                <i class="bi bi-bell-fill"></i>
                                <span class="___class_+?28___" id="notification_count">{{ $notification_count }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right py-0 overflow-hidden not-dropdown">
                        <!-- List group -->
                        <div id="notification_list">

                        </div>
                        <div id="no_notifications" aria-hidden="true" class="dropdown-item text-center py-1">No new
                            notifications</div>
                        <a id="view_all" href="{{ route('notifications-view') }}" class="dropdown-item text-center py-1"
                            style="color: #075db8; font-size: 0.875rem">View
                            all</a>
                    </div>
                </div>
            </div>
            <div id="right_dropdown" class="d-flex order-1 order-md-last">
                <div class="dropdown nav-item">
                    <div class="media align-items-center dropdown-toggle" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" v-pre>
                        @isset($user_data->photo)
                            <img class="rounded-circle avatar" alt="user"
                                src="{{ url('storage/images/' . $user_data->photo) }}" data-holder-rendered="true">
                        @else
                            <div class="avatar-text rounded-circle mx-auto">
                                <span style="color: #e9ecef;"
                                    class="text-center text-uppercase font-weight-bolder">{{ $user_data->first_letter }}</span>
                            </div>
                        @endisset
                        <div class="media-body d-none d-md-block ml-1">
                            <span class="nav-link px-0 pl-1">{{ $user_data->name }}</span>
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right py-0 overflow-hidden">
                        <a class="dropdown-item" href="{{ route('profile') }}">My Profile</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                                                                                                                                                                                                    document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        @endauth
    </nav>
</header>
