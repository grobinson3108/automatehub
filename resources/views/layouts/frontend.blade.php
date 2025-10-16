<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', "Automatehub") }}</title>
    <meta name="description" content="Plateforme d'apprentissage n8n avec système freemium - Automatisation et workflows pour tous">
    
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
    <!-- Only load the weights we actually use in critical CSS -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Font Awesome - optimized loading -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css"></noscript>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Frontend CSS -->
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    
    <!-- Critical CSS inline -->
    <style>
        /* Critical CSS for above-the-fold content */
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
        
        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        
        @media (min-width: 576px) {
            .container {
                max-width: 540px;
            }
        }
        
        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
        }
        
        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
        }
        
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }
    </style>
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet"></noscript>
    
    <!-- Animation on scroll library - deferred -->
    <link rel="preload" href="https://unpkg.com/aos@2.3.1/dist/aos.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"></noscript>
    
    <!-- Custom CSS -->
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
            background-color: var(--cream);
            color: #495057;
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
        
        .btn-primary {
            background-color: var(--infinity-blue);
            border-color: var(--infinity-blue);
        }
        
        .btn-primary:hover {
            background-color: #5a92b2;
            border-color: #5a92b2;
        }
        
        .btn-secondary {
            background-color: var(--pink-medium);
            border-color: var(--pink-medium);
        }
        
        .btn-secondary:hover {
            background-color: #d9a3a3;
            border-color: #d9a3a3;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 10px 0;
        }
        
        .navbar-brand img {
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            padding: 0.5rem;
            margin: 0 0.5rem;
            color: #333;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--infinity-blue);
        }

        .navbar-nav .nav-link i {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 20px;
            height: 20px;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        .navbar-nav .nav-link:hover i {
            transform: scale(1.2);
        }

        /* Effet de soulignement animé */
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--infinity-blue);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 100%;
        }
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background-color: var(--infinity-blue);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
            color: white;
            background-color: #5a92b2;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 60px 0 30px;
            position: relative;
        }
        
        .footer:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--infinity-blue), var(--pink-medium), var(--green-service));
        }
        
        /* AOS animations */
        [data-aos] {
            pointer-events: auto !important;
        }
    </style>
    
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
                        {{-- Tutoriels - À implémenter
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tutorials.*') ? 'active' : '' }}" href="{{ route('tutorials.index') }}">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Tutoriels</span>
                            </a>
                        </li>
                        --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('workflows.*') ? 'active' : '' }}" href="{{ route('workflows.index') }}">
                                <i class="fas fa-project-diagram"></i>
                                <span>Workflows</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('downloads') ? 'active' : '' }}" href="{{ route('downloads') }}">
                                <i class="fas fa-download"></i>
                                <span>Téléchargements</span>
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
                            {{-- Tutoriels - À implémenter
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tutorials.*') ? 'active' : '' }}" href="{{ route('tutorials.index') }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Mes tutoriels</span>
                                </a>
                            </li>
                            --}}
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
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h5 class="text-infinity-blue fw-bold">Automatehub</h5>
                        <p class="mt-3">Plateforme d'apprentissage n8n avec système freemium. Apprenez l'automatisation et les workflows avec des tutoriels pratiques et des ressources de qualité.</p>
                        <div class="mt-4">
                            <a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-4 mb-md-0">
                        <h5 class="text-infinity-blue fw-bold">Apprentissage</h5>
                        <ul class="list-unstyled mt-3">
                            {{-- <li class="mb-2"><a href="{{ route('tutorials.index') }}" class="text-decoration-none text-dark">Tutoriels n8n</a></li> --}}
                            <li class="mb-2"><a href="{{ route('workflows.index') }}" class="text-decoration-none text-dark">Workflows</a></li>
                            <li class="mb-2"><a href="{{ route('downloads') }}" class="text-decoration-none text-dark">Téléchargements</a></li>
                            <li class="mb-2"><a href="{{ route('blog.index') }}" class="text-decoration-none text-dark">Blog</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Automatisation</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Intégrations</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4 mb-md-0">
                        <h5 class="text-infinity-blue fw-bold">Ressources</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Documentation n8n</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Templates</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Exemples pratiques</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">API Reference</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Communauté</a></li>
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
                
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="border-top pt-4">
                            <h6 class="text-infinity-blue fw-bold mb-3">Technologies & Outils</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark">n8n</span>
                                <span class="badge bg-light text-dark">Automatisation</span>
                                <span class="badge bg-light text-dark">Workflows</span>
                                <span class="badge bg-light text-dark">API</span>
                                <span class="badge bg-light text-dark">Intégrations</span>
                                <span class="badge bg-light text-dark">No-code</span>
                                <span class="badge bg-light text-dark">Low-code</span>
                                <span class="badge bg-light text-dark">Zapier</span>
                                <span class="badge bg-light text-dark">Make</span>
                                <span class="badge bg-light text-dark">Webhooks</span>
                                <span class="badge bg-light text-dark">JSON</span>
                                <span class="badge bg-light text-dark">REST API</span>
                                <span class="badge bg-light text-dark">GraphQL</span>
                                <span class="badge bg-light text-dark">Database</span>
                                <span class="badge bg-light text-dark">Cloud</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; {{ date('Y') }} Automatehub. Tous droits réservés. Plateforme d'apprentissage n8n.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('legal') }}" class="text-decoration-none text-dark me-3">Mentions légales</a>
                        <a href="{{ route('privacy-policy') }}" class="text-decoration-none text-dark">Politique de confidentialité</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts deferred -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Initialize AOS with delay -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load AOS with a slight delay to prioritize critical content
            setTimeout(function() {
                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        duration: 800,
                        easing: 'ease-in-out',
                        once: true
                    });
                }
            }, 100);
        });
    </script>
    
    @yield('scripts')
</body>
</html>
