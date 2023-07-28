<nav class="navbar navbar-expand-md shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand text-white" href="#">
            D&TEC
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto"></ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Iniciar sesión') }}</a>
                    </li> -->
                    @if (Route::has('register'))
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.register') }}">{{ __('Registro') }}</a>
                        </li> -->
                    @endif
                @else
                    @include('partials.navigation.'.\App\User::navigation())
                    @include('partials.navigation.logout')
                @endguest
            </ul>
        </div>
    </div>
</nav>