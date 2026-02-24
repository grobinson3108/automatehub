<!doctype html>
<html lang="{{ config('app.locale') }}" class="remember-theme">

<head>
  <meta charset="utf-8">
  <!--
    Available classes for <html> element:

    'dark'                  Enable dark mode - Default dark mode preference can be set in app.js file (always saved and retrieved in localStorage afterwards):
                              window.One = new App({ darkMode: "system" }); // "on" or "off" or "system"
    'dark-custom-defined'   Dark mode is always set based on the preference in app.js file (no localStorage is used)
  -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  <title>@yield('title', 'Automatehub - Backoffice')</title>

  <meta name="description" content="@yield('meta_description', 'Backoffice Automatehub - Plateforme d\'apprentissage n8n')">
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
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

  <!-- Global spacing for content blocks -->
  <style>
    /* Bottom padding inside all OneUI block-content within page .content */
    .content .block > .block-content:last-child {
      padding-bottom: 1.5rem;
    }
    /* Breathing room at the bottom of every page */
    .content {
      padding-bottom: 2rem;
    }
    /* Equal height blocks in rows: stretch columns + blocks + block-content */
    .content .row > [class*="col-"] {
      display: flex;
      flex-direction: column;
    }
    .content .row > [class*="col-"] > .block {
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
    }
    .content .row > [class*="col-"] > .block > .block-content:last-child {
      flex: 1 1 auto;
    }
  </style>

  <!-- Load and set dark mode preference (blocking script to prevent flashing) -->
  <script src="{{ asset('js/setTheme.js') }}"></script>

  <!-- Force Light Theme - Override any dark mode settings -->
  <script src="{{ asset('js/force-light-theme.js') }}"></script>
  
  @yield('js')
</head>

<body>
  <!-- Page Container -->
  <!--
    Available classes for #page-container:

    SIDEBAR and SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Light themed Header
      'page-header-dark'                          Dark themed Header

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)
  -->
  <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <!-- Side Overlay-->
    <aside id="side-overlay" class="fs-sm">
      <!-- Side Header -->
      <div class="content-header border-bottom">
        <!-- User Avatar -->
        <a class="img-link me-1" href="{{ route('user.dashboard') }}">
          <div class="img-avatar img-avatar32 bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
          </div>
        </a>
        <!-- END User Avatar -->

        <!-- User Info -->
        <div class="ms-2">
          <a class="text-dark fw-semibold fs-sm" href="{{ route('user.dashboard') }}">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a>
        </div>
        <!-- END User Info -->

        <!-- Close Side Overlay -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <a class="ms-auto btn btn-sm btn-alt-danger" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_close">
          <i class="fa fa-fw fa-times"></i>
        </a>
        <!-- END Close Side Overlay -->
      </div>
      <!-- END Side Header -->

      <!-- Side Content -->
      <div class="content-side">
        <p>
          <strong>Paramètres utilisateur</strong>
        </p>
        <p>
          Gérez vos préférences et paramètres de compte.
        </p>
      </div>
      <!-- END Side Content -->
    </aside>
    <!-- END Side Overlay -->

    <!-- Sidebar -->
    <!--
        Sidebar Mini Mode - Display Helper classes

        Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
        Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
            If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element

        Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
        Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
        Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
    -->
    <nav id="sidebar" aria-label="Main Navigation">
      <!-- Side Header -->
      <div class="content-header">
        <!-- Logo -->
        <a class="font-semibold text-dual d-flex justify-content-center" href="{{ route('user.dashboard') }}">
          <span class="smini-visible">
            <i class="fa fa-circle-notch text-primary"></i>
          </span>
          <span class="smini-hide fs-5 tracking-wider d-flex justify-content-center">
            <img src="{{ asset('images/logo/Logo_250.png') }}" alt="Automatehub" style="height: 60px; padding: 5px;">
          </span>
        </a>
        <!-- END Logo -->

        <!-- Extra -->
        <div class="d-flex align-items-center gap-1">

          <!-- Close Sidebar, Visible only on mobile screens -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
            <i class="fa fa-fw fa-times"></i>
          </a>
          <!-- END Close Sidebar -->
        </div>
        <!-- END Extra -->
      </div>
      <!-- END Side Header -->

      <!-- Sidebar Scrolling -->
      <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
        <div class="content-side">
          <ul class="nav-main">
            <!-- Dashboard Principal -->
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('user.dashboard') ? ' active' : '' }}" href="{{ route('user.dashboard') }}">
                <i class="nav-main-link-icon si si-speedometer"></i>
                <span class="nav-main-link-name">Tableau de bord</span>
              </a>
            </li>
            
            <!-- Section Mini-Apps -->
            <li class="nav-main-heading">Mini-Apps</li>

            {{-- Marketplace --}}
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('apps.index') ? ' active' : '' }}" href="{{ route('apps.index') }}">
                <i class="nav-main-link-icon fas fa-th-large"></i>
                <span class="nav-main-link-name">Toutes les Apps</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('my-apps.index') ? ' active' : '' }}" href="{{ route('my-apps.index') }}">
                <i class="nav-main-link-icon fas fa-rocket"></i>
                <span class="nav-main-link-name">Mes Apps</span>
              </a>
            </li>

            {{-- Mes Apps Actives (conditionnel) --}}
            @auth
            @php
              $sidebarActiveSubscriptions = auth()->user()
                ->appSubscriptions()
                ->with('app')
                ->whereIn('status', ['active', 'trial'])
                ->get()
                ->filter(fn($sub) => $sub->hasAccess() && $sub->app !== null);

              $appDashboardRoutes = [
                'videoplan'  => 'videoplan.projects.index',
                'watchtrend' => 'watchtrend.dashboard',
                'orderflow'  => 'orderflow.dashboard.index',
              ];

              $appIcons = [
                'videoplan'  => 'fas fa-video',
                'watchtrend' => 'fas fa-binoculars',
                'orderflow'  => 'fas fa-shopping-cart',
                'postmaid'   => 'fas fa-paper-plane',
                'vocamail'   => 'fas fa-microphone',
              ];
            @endphp
            @if($sidebarActiveSubscriptions->isNotEmpty())
              <li class="nav-main-heading">Mes Apps Actives</li>
              @foreach($sidebarActiveSubscriptions as $sidebarSub)
                @php
                  $appSlug = $sidebarSub->app->slug;
                  $appName = $sidebarSub->app->name;
                  $appIcon = $appIcons[$appSlug] ?? 'fas fa-puzzle-piece';
                  $hasDedicatedRoute = isset($appDashboardRoutes[$appSlug]);
                @endphp
                <li class="nav-main-item">
                  @if($hasDedicatedRoute)
                    @php
                      try {
                        $appDashboardUrl = route($appDashboardRoutes[$appSlug]);
                      } catch (\Exception $e) {
                        $appDashboardUrl = route('my-apps.dashboard', $appSlug);
                      }
                    @endphp
                    <a class="nav-main-link" href="{{ $appDashboardUrl }}">
                      <i class="nav-main-link-icon {{ $appIcon }}"></i>
                      <span class="nav-main-link-name">{{ $appName }}</span>
                      @if($sidebarSub->onTrial())
                        <span class="nav-main-link-badge badge rounded-pill bg-warning">Essai</span>
                      @endif
                    </a>
                  @else
                    <a class="nav-main-link" href="{{ route('my-apps.dashboard', $appSlug) }}">
                      <i class="nav-main-link-icon {{ $appIcon }}"></i>
                      <span class="nav-main-link-name">{{ $appName }}</span>
                      @if($sidebarSub->onTrial())
                        <span class="nav-main-link-badge badge rounded-pill bg-warning">Essai</span>
                      @endif
                    </a>
                  @endif
                </li>
              @endforeach
            @endif
            @endauth

            <!-- Section Administration (visible seulement pour les admins) -->
            @if(Auth::user()->is_admin)
            <li class="nav-main-heading">Administration</li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="nav-main-link-icon si si-speedometer"></i>
                <span class="nav-main-link-name">Dashboard Admin</span>
              </a>
            </li>
            <li class="nav-main-item{{ request()->routeIs('admin.users.*') ? ' open' : '' }}">
              <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" href="#">
                <i class="nav-main-link-icon si si-users"></i>
                <span class="nav-main-link-name">Gestion Utilisateurs</span>
              </a>
              <ul class="nav-main-submenu">
                <li class="nav-main-item">
                  <a class="nav-main-link{{ request()->routeIs('admin.users.index') ? ' active' : '' }}" href="{{ route('admin.users.index') }}">
                    <span class="nav-main-link-name">Tous les utilisateurs</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link{{ request()->routeIs('admin.users.subscriptions') ? ' active' : '' }}" href="{{ route('admin.users.subscriptions') }}">
                    <span class="nav-main-link-name">Abonnements</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link{{ request()->routeIs('admin.users.activity') ? ' active' : '' }}" href="{{ route('admin.users.activity') }}">
                    <span class="nav-main-link-name">Activités</span>
                  </a>
                </li>
              </ul>
            </li>
            {{-- Mini-Apps Management --}}
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('admin.apps.*') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="nav-main-link-icon fas fa-th-large"></i>
                <span class="nav-main-link-name">Gestion Apps</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('admin.analytics.*') ? ' active' : '' }}" href="{{ route('admin.analytics.dashboard') }}">
                <i class="nav-main-link-icon si si-graph"></i>
                <span class="nav-main-link-name">Analytics</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('admin.finances.*') ? ' active' : '' }}" href="{{ route('admin.finances.dashboard') }}">
                <i class="nav-main-link-icon si si-wallet"></i>
                <span class="nav-main-link-name">Finances</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('admin.contacts.*') ? ' active' : '' }}" href="{{ route('admin.contacts.index') }}">
                <i class="nav-main-link-icon si si-envelope"></i>
                <span class="nav-main-link-name">Messages Contact</span>
              </a>
            </li>
            {{-- Masterclass link disabled (route not defined) --}}
            <li class="nav-main-item{{ request()->routeIs('admin.video-content.*') || request()->routeIs('admin.publication-calendar.*') ? ' open' : '' }}">
              <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.video-content.*') || request()->routeIs('admin.publication-calendar.*') ? 'true' : 'false' }}" href="#">
                <i class="nav-main-link-icon fa fa-video"></i>
                <span class="nav-main-link-name">Contenu Vidéo</span>
                <span class="nav-main-link-badge badge rounded-pill bg-primary">Nouveau</span>
              </a>
              <ul class="nav-main-submenu">
                <li class="nav-main-item">
                  <a class="nav-main-link{{ request()->routeIs('admin.video-content.*') ? ' active' : '' }}" href="{{ route('admin.video-content.index') }}">
                    <span class="nav-main-link-name">Plans Vidéo</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link{{ request()->routeIs('admin.publication-calendar.*') ? ' active' : '' }}" href="{{ route('admin.publication-calendar.index') }}">
                    <span class="nav-main-link-name">Calendrier Publication</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link{{ request()->routeIs('admin.publication-calendar.today') ? ' active' : '' }}" href="{{ route('admin.publication-calendar.today') }}">
                    <span class="nav-main-link-name">Planning Aujourd'hui</span>
                  </a>
                </li>
              </ul>
            </li>

            {{-- Workflow translation link disabled (route not defined) --}}
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('admin.settings.*') ? ' active' : '' }}" href="{{ route('admin.settings.index') }}">
                <i class="nav-main-link-icon si si-settings"></i>
                <span class="nav-main-link-name">Paramètres</span>
              </a>
            </li>
            @endif
            
            <!-- Section Compte -->
            <li class="nav-main-heading">Compte</li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('user.profile.*') ? ' active' : '' }}" href="{{ route('user.profile.index') }}">
                <i class="nav-main-link-icon si si-user"></i>
                <span class="nav-main-link-name">Mon profil</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('user.subscription.*') ? ' active' : '' }}" href="{{ route('user.subscription.index') }}">
                <i class="nav-main-link-icon si si-credit-card"></i>
                <span class="nav-main-link-name">Abonnement</span>
                <span class="nav-main-link-badge badge rounded-pill bg-{{ Auth::user()->subscription_type === 'free' ? 'secondary' : 'success' }}">
                  {{ ucfirst(Auth::user()->subscription_type) }}
                </span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link{{ request()->routeIs('user.notifications.*') ? ' active' : '' }}" href="{{ route('user.notifications.index') }}">
                <i class="nav-main-link-icon si si-bell"></i>
                <span class="nav-main-link-name">Notifications</span>
              </a>
            </li>

            <!-- Section Exploration -->
            <li class="nav-main-heading">Exploration</li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="{{ route('home') }}" target="_blank">
                <i class="nav-main-link-icon si si-globe"></i>
                <span class="nav-main-link-name">Site public</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="{{ route('blog.index') }}" target="_blank">
                <i class="nav-main-link-icon si si-note"></i>
                <span class="nav-main-link-name">Blog</span>
              </a>
            </li>
          </ul>
        </div>
        <!-- END Side Navigation -->
      </div>
      <!-- END Sidebar Scrolling -->
    </nav>
    <!-- END Sidebar -->

    <!-- Header -->
    <header id="page-header">
      <!-- Header Content -->
      <div class="content-header">
        <!-- Left Section -->
        <div class="d-flex align-items-center">
          <!-- Toggle Sidebar -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
          <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
            <i class="fa fa-fw fa-bars"></i>
          </button>
          <!-- END Toggle Sidebar -->

          <!-- Open Search Section (visible on smaller screens) -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <button type="button" class="btn btn-sm btn-alt-secondary d-md-none" data-toggle="layout" data-action="header_search_on">
            <i class="fa fa-fw fa-search"></i>
          </button>
          <!-- END Open Search Section -->

          <!-- Search Form (visible on larger screens) -->
          <form class="d-none d-md-inline-block" action="{{ route('apps.index') }}" method="GET">
            <div class="input-group input-group-sm">
              <input type="text" class="form-control form-control-alt" placeholder="Rechercher une app..." id="page-header-search-input2" name="q">
              <span class="input-group-text border-0">
                <i class="fa fa-fw fa-search"></i>
              </span>
            </div>
          </form>
          <!-- END Search Form -->
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="d-flex align-items-center">
          <!-- User Dropdown -->
          <div class="dropdown d-inline-block ms-2">
            <button type="button" class="btn btn-sm btn-alt-secondary d-flex align-items-center" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 21px; height: 21px; font-size: 10px;">
                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
              </div>
              <span class="d-none d-sm-inline-block ms-2">{{ Auth::user()->first_name }}</span>
              <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ms-1 mt-1"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0 border-0" aria-labelledby="page-header-user-dropdown">
              <div class="p-3 text-center bg-body-light border-bottom rounded-top">
                <div class="img-avatar img-avatar48 bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto" style="font-size: 18px;">
                  {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <p class="mt-2 mb-0 fw-medium">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <p class="mb-0 text-muted fs-sm fw-medium">{{ Auth::user()->email }}</p>
                @if(Auth::user()->is_admin)
                  <span class="badge bg-danger mt-1">Administrateur</span>
                @endif
                @php
                  $headerActiveApps = Auth::user()->appSubscriptions()
                    ->whereIn('status', ['active', 'trial'])
                    ->count();
                @endphp
                @if($headerActiveApps > 0)
                  <span class="badge bg-primary mt-1">{{ $headerActiveApps }} app{{ $headerActiveApps > 1 ? 's' : '' }} active{{ $headerActiveApps > 1 ? 's' : '' }}</span>
                @endif
              </div>
              <div class="p-2">
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('user.dashboard') }}">
                  <span class="fs-sm fw-medium"><i class="si si-speedometer me-2"></i>Tableau de bord</span>
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('my-apps.index') }}">
                  <span class="fs-sm fw-medium"><i class="fas fa-rocket me-2"></i>Mes Apps</span>
                  @if($headerActiveApps > 0)
                    <span class="badge bg-primary">{{ $headerActiveApps }}</span>
                  @endif
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('apps.index') }}">
                  <span class="fs-sm fw-medium"><i class="fas fa-store me-2"></i>Marketplace</span>
                </a>
              </div>
              <div role="separator" class="dropdown-divider m-0"></div>
              <div class="p-2">
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('user.profile.index') }}">
                  <span class="fs-sm fw-medium"><i class="si si-user me-2"></i>Mon profil</span>
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('user.subscription.index') }}">
                  <span class="fs-sm fw-medium"><i class="si si-credit-card me-2"></i>Abonnement</span>
                  <span class="badge bg-{{ Auth::user()->subscription_type === 'free' ? 'secondary' : 'success' }}">{{ ucfirst(Auth::user()->subscription_type) }}</span>
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('user.notifications.index') }}">
                  <span class="fs-sm fw-medium"><i class="si si-bell me-2"></i>Notifications</span>
                </a>
              </div>
              <div role="separator" class="dropdown-divider m-0"></div>
              <div class="p-2">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                  @csrf
                  <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between text-danger">
                    <span class="fs-sm fw-medium"><i class="si si-logout me-2"></i>Déconnexion</span>
                  </button>
                </form>
              </div>
            </div>
          </div>
          <!-- END User Dropdown -->

          <!-- Notifications Dropdown -->
          <div class="dropdown d-inline-block ms-2">
            <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-fw fa-bell"></i>
              <span class="text-primary">•</span>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 border-0 fs-sm" aria-labelledby="page-header-notifications-dropdown">
              <div class="p-2 bg-body-light border-bottom text-center rounded-top">
                <h5 class="dropdown-header text-uppercase">Notifications</h5>
              </div>
              <ul class="nav-items mb-0">
                <li>
                  <a class="text-dark d-flex py-2" href="{{ route('apps.index') }}">
                    <div class="flex-shrink-0 me-2 ms-3">
                      <i class="fa fa-fw fa-rocket text-primary"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                      <div class="fw-semibold">Bienvenue sur AutomateHub</div>
                      <span class="fw-medium text-muted">Explorez nos mini-apps IA</span>
                    </div>
                  </a>
                </li>
              </ul>
              <div class="p-2 border-top">
                <a class="btn btn-sm btn-light d-block text-center" href="{{ route('user.notifications.index') }}">
                  <i class="fa fa-fw fa-arrow-down me-1"></i> Voir toutes les notifications
                </a>
              </div>
            </div>
          </div>
          <!-- END Notifications Dropdown -->

          <!-- Toggle Side Overlay -->
          <button type="button" class="btn btn-sm btn-alt-secondary ms-2" data-toggle="layout" data-action="side_overlay_toggle">
            <i class="fa fa-fw fa-list-ul fa-flip-horizontal"></i>
          </button>
          <!-- END Toggle Side Overlay -->
        </div>
        <!-- END Right Section -->
      </div>
      <!-- END Header Content -->
    </header>
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container">
      @yield('content')
    </main>
    <!-- END Main Container -->

    <!-- Footer -->
    <footer id="page-footer" class="bg-body-extra-light">
      <div class="content py-3">
        <div class="row fs-sm">
          <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
            Créé avec <i class="fa fa-heart text-danger"></i> par <a class="fw-semibold" href="https://audelalia.fr" target="_blank">Audelalia</a>
          </div>
          <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
            <a class="fw-semibold" href="https://automatehub.fr" target="_blank">AutomateHub</a> &copy; <span data-toggle="year-copy"></span>
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->
  </div>
  <!-- END Page Container -->

  <!-- jQuery (required for OneUI) -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

  <!-- OneUI Core JS -->
  <script src="{{ asset('oneui/js/oneui.app.min.js') }}"></script>

  <!-- Page JS Plugins -->
  @yield('js')

  <!-- Page JS Code -->
  @yield('js_after')

  <!-- Custom JS -->
  <script>
    One.helpersOnLoad(['jq-appear', 'jq-countto', 'jq-sparkline']);
  </script>
</body>

</html>
