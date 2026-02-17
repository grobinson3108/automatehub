<!doctype html>
<html lang="{{ config('app.locale') }}" class="remember-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  <title>@yield('title', 'WatchTrend') - AutomateHub</title>

  <meta name="description" content="WatchTrend - Veille intelligente multi-sources avec analyse IA">
  <meta name="author" content="Automatehub">
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons -->
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('favicon.svg') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

  <!-- Modules -->
  @yield('css')

  <!-- OneUI CSS -->
  <link rel="stylesheet" id="css-main" href="{{ asset('oneui/css/oneui.min.css') }}">
  <link rel="stylesheet" id="css-theme" href="{{ asset('oneui/css/themes/audelalia.min.css') }}">

  <!-- App Theme (dynamic colors) -->
  @if(isset($currentApp))
    @include('layouts.partials.app-theme', ['app' => $currentApp])
  @endif

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{ asset('oneui/js/plugins/sweetalert2/sweetalert2.css') }}">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

  <!-- Load and set dark mode preference -->
  <script src="{{ asset('js/setTheme.js') }}"></script>
  <script src="{{ asset('js/force-light-theme.js') }}"></script>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/bootstrap-init.js') }}"></script>

  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  @yield('js')
</head>

<body>
  <!-- Page Container -->
  <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed main-content-narrow">

    <!-- Sidebar -->
    <nav id="sidebar" aria-label="Main Navigation">
      <!-- Side Header -->
      <div class="content-header">
        <a class="font-semibold text-dual d-flex justify-content-center" href="{{ route('watchtrend.dashboard') }}">
          <span class="smini-visible">
            <i class="fa fa-binoculars text-app"></i>
          </span>
          <span class="smini-hide fs-5 tracking-wider d-flex align-items-center">
            <i class="fa fa-binoculars text-app me-2"></i>
            <span class="fw-bold">WatchTrend</span>
          </span>
        </a>

        <div class="d-flex align-items-center gap-1">
          <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
            <i class="fa fa-fw fa-times"></i>
          </a>
        </div>
      </div>

      <!-- Bouton Retour AutomateHub -->
      <div class="content-side border-bottom pb-3 mb-2">
        <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-app-outline w-100 d-flex align-items-center justify-content-center">
          <i class="fa fa-arrow-left me-2"></i>
          <span class="smini-hide">Retour AutomateHub</span>
        </a>
      </div>

      <!-- Sidebar Scrolling -->
      <div class="js-sidebar-scroll">
        <div class="content-side">
          <ul class="nav-main">
            <li class="nav-main-heading text-uppercase text-muted fs-xs">Veille</li>

            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('watchtrend.dashboard') ? ' active' : '' }}" href="{{ route('watchtrend.dashboard') }}">
                <i class="nav-main-link-icon fa fa-tachometer-alt"></i>
                <span class="nav-main-link-name">Dashboard</span>
              </a>
            </li>

            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('watchtrend.watches.*') ? ' active' : '' }}" href="{{ route('watchtrend.watches.index') }}">
                <i class="nav-main-link-icon fa fa-binoculars"></i>
                <span class="nav-main-link-name">Mes Watches</span>
              </a>
            </li>

            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('watchtrend.sources.*') ? ' active' : '' }}" href="{{ route('watchtrend.sources.index') }}">
                <i class="nav-main-link-icon fa fa-rss"></i>
                <span class="nav-main-link-name">Sources</span>
              </a>
            </li>

            <li class="nav-main-heading text-uppercase text-muted fs-xs mt-3">Analyse</li>

            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('watchtrend.pain-points.*') ? ' active' : '' }}" href="{{ route('watchtrend.pain-points.index') }}">
                <i class="nav-main-link-icon fa fa-exclamation-triangle"></i>
                <span class="nav-main-link-name">Pain Points</span>
              </a>
            </li>

            <li class="nav-main-heading text-uppercase text-muted fs-xs mt-3">Configuration</li>

            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('watchtrend.settings.*') ? ' active' : '' }}" href="{{ route('watchtrend.settings.index') }}">
                <i class="nav-main-link-icon fa fa-cog"></i>
                <span class="nav-main-link-name">Parametres</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- END Sidebar -->

    <!-- Header -->
    <header id="page-header">
      <div class="content-header">
        <div class="d-flex align-items-center">
          <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
            <i class="fa fa-fw fa-bars"></i>
          </button>
          <h1 class="h5 mb-0 text-dark fw-semibold">
            @yield('page-title', 'WatchTrend')
          </h1>
        </div>

        <div class="d-flex align-items-center">
          <!-- User Dropdown -->
          <div class="dropdown d-inline-block">
            <button type="button" class="btn btn-sm btn-alt-secondary d-flex align-items-center" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="background:var(--app-accent,#059669); width: 21px; height: 21px; font-size: 10px;">
                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
              </div>
              <span class="d-none d-sm-inline-block ms-2">{{ Auth::user()->first_name }}</span>
              <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ms-1 mt-1"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 border-0" aria-labelledby="page-header-user-dropdown">
              <div class="p-3 text-center bg-body-light border-bottom rounded-top">
                <div class="img-avatar img-avatar48 text-white d-flex align-items-center justify-content-center fw-bold mx-auto" style="font-size: 18px; background:var(--app-accent,#059669);">
                  {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <p class="mt-2 mb-0 fw-medium">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <p class="mb-0 text-muted fs-sm fw-medium">{{ Auth::user()->email }}</p>
              </div>
              <div class="p-2">
                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.dashboard') }}">
                  <i class="fa fa-home me-2"></i> Dashboard AutomateHub
                </a>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.profile.index') }}">
                  <i class="fa fa-user me-2"></i> Mon Profil
                </a>
              </div>
              <div role="separator" class="dropdown-divider m-0"></div>
              <div class="p-2">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                  @csrf
                  <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                    <i class="fa fa-sign-out-alt me-2"></i> Deconnexion
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container">
      <!-- Hero -->
      <div class="bg-body-light">
        <div class="content content-full">
          <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@yield('page-title', 'WatchTrend')</h1>
            <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{ route('watchtrend.dashboard') }}">WatchTrend</a>
                </li>
                @yield('breadcrumb')
              </ol>
            </nav>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <div class="content">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @yield('content')
      </div>
    </main>

    <!-- Footer -->
    <footer id="page-footer" class="bg-body-extra-light">
      <div class="content py-3">
        <div class="row fs-sm">
          <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
            <span class="text-muted">WatchTrend</span> by <a class="fw-semibold" href="https://automatehub.fr" target="_blank">AutomateHub</a>
          </div>
          <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
            <a class="fw-semibold" href="https://automatehub.fr" target="_blank">AutomateHub</a> &copy; <span data-toggle="year-copy"></span>
          </div>
        </div>
      </div>
    </footer>
  </div>

  <!-- jQuery (required for OneUI) -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

  <!-- OneUI Core JS -->
  <script src="{{ asset('oneui/js/oneui.app.min.js') }}"></script>

  <!-- SweetAlert2 -->
  <script src="{{ asset('oneui/js/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

  <!-- Global Modal Helper (WTModal) -->
  <script>
    window.WTModal = {
      defaults: {
        confirmButtonColor: '{{ $currentApp->color_accent ?? '#059669' }}',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Confirmer',
        cancelButtonText: 'Annuler',
      },
      confirm: function(options = {}) {
        return Swal.fire({
          title: options.title || 'Etes-vous sur ?',
          text: options.text || '',
          icon: options.icon || 'question',
          showCancelButton: true,
          confirmButtonColor: this.defaults.confirmButtonColor,
          cancelButtonColor: this.defaults.cancelButtonColor,
          confirmButtonText: options.confirmText || this.defaults.confirmButtonText,
          cancelButtonText: options.cancelText || this.defaults.cancelButtonText,
          reverseButtons: true,
        });
      },
      delete: function(options = {}) {
        const itemName = options.itemName ? `"${options.itemName}"` : 'cet element';
        return Swal.fire({
          title: options.title || 'Supprimer ?',
          text: options.text || `Voulez-vous vraiment supprimer ${itemName} ? Cette action est irreversible.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: this.defaults.cancelButtonColor,
          confirmButtonText: options.confirmText || '<i class="fa fa-trash me-1"></i> Supprimer',
          cancelButtonText: options.cancelText || 'Annuler',
          reverseButtons: true,
        });
      },
      toast: function(type, title) {
        const Toast = Swal.mixin({
          toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
        });
        Toast.fire({ icon: type, title: title });
      },
      close: function() { Swal.close(); }
    };
  </script>

  @yield('js_plugins')
  @yield('js_after')

  <script>
    One.helpersOnLoad(['jq-appear', 'jq-countto', 'jq-sparkline']);
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof bootstrap !== 'undefined') {
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        dropdownElementList.map(function (el) { return new bootstrap.Dropdown(el); });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
      }
    });
  </script>

  @stack('scripts')
</body>

</html>
