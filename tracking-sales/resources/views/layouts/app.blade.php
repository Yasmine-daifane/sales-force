<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ config('app.name', 'Force') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link href="{{ asset('css/custom_navbar.css') }}" rel="stylesheet">

<script src="{{ asset('js/ocr-scanner.js') }}"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <script src="{{ asset('js/factures.js') }}"></script>


</head>
<body>
    <div id="app">
        <!-- Apply a custom class for easier styling -->
        <nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm custom-navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <!-- Assurez-vous que le chemin vers votre logo est correct -->
                    <img src="{{ asset('png/backg.png') }}" alt="Force Logo" width="100" height="auto"> <!-- Hauteur ajustée à auto pour conserver les proportions -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Liens à gauche (Spécifiques Admin/Commercial) -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if (Auth::user()->role === 'admin')
                                <li class="nav-item">
                                    <!-- Ajout de la vérification de route active et d'une icône -->
                                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                        <i class="bi bi-house-door-fill me-1"></i> Home
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                        <i class="bi bi-people-fill me-1"></i> Commerciaux
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('factures.index') ? 'active' : '' }}" href="{{ route('factures.index') }}">
                                        <i class="bi bi-receipt-cutoff me-1"></i> Factures <!-- Correction typo: Fctures -> Factures -->
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('visits.index') ? 'active' : '' }}" href="{{ route('visits.index') }}">
                                        <i class="bi bi-cart-check-fill me-1"></i> Ventes
                                    </a>
                                </li>
                            @elseif (Auth::user()->role === 'commercial')

                             <li class="nav-item">
                                    <!-- Ajout de la vérification de route active et d'une icône -->
                                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                        <i class="bi bi-house-door-fill me-1"></i> Home
                                    </a>
                                </li>
                                <li class="nav-item">


                                     <!-- Assumant que 'visits.index' est la page principale pour les commerciaux -->
                                    <a class="nav-link {{ request()->routeIs('visits.index') ? 'active' : '' }}" href="{{ route('visits.index') }}">
                                        <i class="bi bi-calendar-check-fill me-1"></i> Mes Visites
                                    </a>


                                </li>
                                <!-- Ajoutez d'autres liens spécifiques aux commerciaux ici si nécessaire -->
                            @endif
                        @endauth
                        <!-- Ajoutez des liens publics ici si nécessaire -->
                    </ul>

                    <!-- Liens à droite (Authentification) -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('Login') }}
                                    </a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus-fill me-1"></i> {{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-1"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>

                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <!-- ... (reste du body) ... -->
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <!-- ... -->
        @stack('scripts')
</body>
</html>




