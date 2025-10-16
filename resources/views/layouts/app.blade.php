<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Automatehub'))</title>
    <meta name="description" content="@yield('description', 'Plateforme d\'apprentissage n8n avec système freemium - Automatisation et workflows pour tous')">
    
    <!-- Canonical URL -->
    @if(isset($canonicalUrl))
        <link rel="canonical" href="{{ $canonicalUrl }}" />
    @else
        <link rel="canonical" href="{{ url()->current() }}" />
    @endif

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">    
    
    <!-- Fonts - optimized loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Font Awesome -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css"></noscript>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    
    <!-- Critical CSS -->
    <style>
        :root {
            --infinity-blue: #6ba3c3;
            --pink-medium: #e8b4b4;
            --green-service: #54b48d;
            --warning-service: #e9c46a;
            --danger-service: #e76f51;
            --beige-bg: #d7ceb2;
            --pink-light: #f3d5d5;
            --blue-light: #b4d8e8;
            --blue-medium: #7a9cb5;
            --cream: #f9f6f0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            margin: 0;
            background-color: var(--cream);
            color: #495057;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 10px 0;
            background-color: #fff;
        }
        
        .navbar-brand img {
            height: 70px;
        }
        
        .btn-primary {
            background-color: var(--infinity-blue);
            border-color: var(--infinity-blue);
        }
        
        .btn-primary:hover {
            background-color: #5a92b2;
            border-color: #5a92b2;
        }
        
        .text-infinity-blue {
            color: var(--infinity-blue) !important;
        }
        
        .text-pink-medium {
            color: var(--pink-medium) !important;
        }
        
        .text-green-service {
            color: var(--green-service) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo/Logo_250.png') }}" alt="Automatehub" height="70" class="me-2">
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('workflows.*') ? 'active' : '' }}" href="{{ route('workflows.index') }}">
                                <i class="fas fa-project-diagram"></i>
                                <span>Workflows</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('packs.*') ? 'active' : '' }}" href="{{ route('packs.index') }}">
                                <i class="fas fa-box-open"></i>
                                <span>Packs Premium</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">
                                <i class="fas fa-blog"></i>
                                <span>Blog</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                                <i class="fas fa-envelope"></i>
                                <span>Contact</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Connexion</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i>
                                    <span>S'inscrire</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Tableau de bord</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i>
                                    <span>{{ Auth::user()->first_name }}</span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i>Mon profil
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-cog me-2"></i>Paramètres
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="footer bg-light py-5 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h5 class="text-infinity-blue fw-bold">Automatehub</h5>
                        <p class="mt-3">Plateforme d'apprentissage n8n avec système freemium. Apprenez l'automatisation et les workflows avec des tutoriels pratiques et des ressources de qualité.</p>
                        <div class="mt-4">
                            <a href="#" class="social-icon me-2" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon me-2" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon me-2" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-4 mb-md-0">
                        <h5 class="text-infinity-blue fw-bold">Apprentissage</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><a href="{{ route('workflows.index') }}" class="text-decoration-none text-dark">Workflows</a></li>
                            <li class="mb-2"><a href="{{ route('downloads') }}" class="text-decoration-none text-dark">Téléchargements</a></li>
                            <li class="mb-2"><a href="{{ route('blog.index') }}" class="text-decoration-none text-dark">Blog</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4 mb-md-0">
                        <h5 class="text-infinity-blue fw-bold">Ressources</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Documentation n8n</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Templates</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Exemples pratiques</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">API Reference</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h5 class="text-infinity-blue fw-bold">Contact</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class="fas fa-envelope me-2 text-pink-medium"></i> <a href="mailto:contact@automatehub.fr" class="text-decoration-none text-dark">contact@automatehub.fr</a></li>
                            <li class="mb-2"><i class="fas fa-globe me-2 text-pink-medium"></i> France</li>
                        </ul>
                        <div class="mt-3">
                            <a href="{{ route('contact') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                Nous contacter
                            </a>
                        </div>
                    </div>
                </div>
                
                <hr class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; {{ date('Y') }} Automatehub. Tous droits réservés.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('legal') }}" class="text-decoration-none text-dark me-3">Mentions légales</a>
                        <a href="{{ route('privacy-policy') }}" class="text-decoration-none text-dark">Politique de confidentialité</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>