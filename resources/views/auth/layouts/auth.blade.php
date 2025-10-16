<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name', 'Automatehub') }} - @yield('title')</title>

    <meta name="description" content="@yield('meta_description', 'Automatehub - Plateforme d\'apprentissage n8n avec système freemium')">
    <meta name="author" content="Automatehub">
    <meta name="robots" content="index, follow">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="{{ asset('oneui/css/oneui.min.css') }}">
    <link rel="stylesheet" id="css-theme" href="{{ asset('oneui/css/themes/amethyst.min.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <!-- END Stylesheets -->

    <!-- Load and set color theme + dark mode preference (blocking script to prevent flashing) -->
    <script src="{{ asset('oneui/js/setTheme.js') }}"></script>
  </head>

  <body>
    <!-- Page Container -->
    <div id="page-container">
      <!-- Main Container -->
      <main id="main-container">
        <!-- Page Content -->
        <div class="@yield('bg-class', 'bg-primary')" @if(View::hasSection('bg-image')) style="background-image: url('@yield('bg-image')');" @endif>
          <div class="row g-0 @yield('bg-class', 'bg-primary')-dark-op">
            <!-- Meta Info Section -->
            <div class="hero-static col-lg-4 d-none d-lg-flex flex-column justify-content-center">
              <div class="p-4 p-xl-5 flex-grow-1 d-flex align-items-center">
                <div class="w-100">
                  <a class="link-fx fw-semibold fs-2 text-white d-flex align-items-center" href="/">
                    <img src="{{ asset('images/logo/Logo_250.png') }}" alt="Automatehub" height="40" class="me-2">
                    {{ config('app.name', 'Automatehub') }}
                  </a>
                  <p class="text-white-75 me-xl-8 mt-2">
                    @yield('sidebar_description', "Bienvenue sur la plateforme d'apprentissage n8n avec système freemium.")
                  </p>
                </div>
              </div>
              <div class="p-4 p-xl-5 d-xl-flex justify-content-between align-items-center fs-sm">
                <p class="fw-medium text-white-50 mb-0">
                  <strong>{{ config('app.name', 'Automatehub') }}</strong> &copy; {{ date('Y') }}
                </p>
                <ul class="list list-inline mb-0 py-2">
                  <li class="list-inline-item">
                    <a class="text-white-75 fw-medium" href="/mentions-legales">Mentions légales</a>
                  </li>
                  <li class="list-inline-item">
                    <a class="text-white-75 fw-medium" href="/contact">Contact</a>
                  </li>
                  <li class="list-inline-item">
                    <a class="text-white-75 fw-medium" href="/cgv">CGV</a>
                  </li>
                </ul>
              </div>
            </div>
            <!-- END Meta Info Section -->

            <!-- Main Section -->
            <div class="hero-static col-lg-8 d-flex flex-column align-items-center bg-body-extra-light">
              <div class="p-3 w-100 d-lg-none text-center">
                <a class="link-fx fw-semibold fs-3 text-dark d-flex align-items-center justify-content-center" href="/">
                  <img src="{{ asset('images/logo/Logo_250.png') }}" alt="Automatehub" height="40" class="me-2">
                  {{ config('app.name', 'Automatehub') }}
                </a>
              </div>
              <div class="p-4 w-100 flex-grow-1 d-flex align-items-center">
                <div class="w-100">
                  <!-- Header -->
                  <div class="text-center mb-5">
                    <p class="mb-3">
                      <i class="fa fa-2x fa-circle-notch text-primary-light"></i>
                    </p>
                    <h1 class="fw-bold mb-2">
                      @yield('title')
                    </h1>
                    <p class="fw-medium text-muted">
                      @yield('description')
                    </p>
                  </div>
                  <!-- END Header -->

                  <!-- Form Content -->
                  <div class="row g-0 justify-content-center">
                    <div class="col-sm-8 col-xl-4">
                      @yield('content')
                    </div>
                  </div>
                  <!-- END Form Content -->
                </div>
              </div>
              <div class="px-4 py-3 w-100 d-lg-none d-flex flex-column flex-sm-row justify-content-between fs-sm text-center text-sm-start">
                <p class="fw-medium text-black-50 py-2 mb-0">
                  <strong>{{ config('app.name', 'Automatehub') }}</strong> &copy; {{ date('Y') }}
                </p>
                <ul class="list list-inline py-2 mb-0">
                  <li class="list-inline-item">
                    <a class="text-muted fw-medium" href="/mentions-legales">Mentions légales</a>
                  </li>
                  <li class="list-inline-item">
                    <a class="text-muted fw-medium" href="/contact">Contact</a>
                  </li>
                  <li class="list-inline-item">
                    <a class="text-muted fw-medium" href="/cgv">CGV</a>
                  </li>
                </ul>
              </div>
            </div>
            <!-- END Main Section -->
          </div>
        </div>
        <!-- END Page Content -->
      </main>
      <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <!-- jQuery (required for jQuery Validation plugin) -->
    <script src="{{ asset('oneui/js/lib/jquery.min.js') }}"></script>
    
    <!-- OneUI Core JS -->
    <script src="{{ asset('oneui/js/oneui.app.min.js') }}"></script>
    
    <!-- jQuery Validation Plugin -->
    <script src="{{ asset('oneui/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    
    <!-- Page JS Code -->
    @yield('js_after')
  </body>
</html>
