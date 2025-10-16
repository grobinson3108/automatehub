<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name', 'AutomateHub') }} - Connexion</title>

    <meta name="description" content="Bienvenue sur AutomateHub. Connectez-vous pour accéder à votre espace et gérer vos projets d'automatisation n8n.">
    <meta name="author" content="AutomateHub">
    <meta name="robots" content="index, follow">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="{{ asset('oneui/css/oneui.min.css') }}">
    <link rel="stylesheet" id="css-theme" href="{{ asset('oneui/css/themes/audelalia.min.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <!-- END Stylesheets -->

    <!-- Force light mode and Audelalia theme for auth pages -->
    <script>
    // Force light mode and Audelalia theme for authentication pages
    let lHtml = document.documentElement;
    
    // Remove dark mode if present
    lHtml.classList.remove("dark");
    
    // Ensure Audelalia theme is applied
    let themeEl = document.getElementById("css-theme");
    if (themeEl) {
        themeEl.setAttribute("href", "{{ asset('oneui/css/themes/audelalia.min.css') }}");
    }
    </script>
  </head>

  <body>
    <!-- Page Container -->
    <div id="page-container">
      <!-- Main Container -->
      <main id="main-container">
        <!-- Page Content -->
        <div class="bg-image" style="background-image: url('{{ asset('oneui/media/photos/photo28@2x.jpg') }}');">
          <div class="row g-0">
            <!-- Meta Info Section -->
            <div class="hero-static col-lg-4 d-none d-lg-flex flex-column justify-content-center" style="background-color: rgba(0, 0, 0, 0.6);">
              <div class="p-4 p-xl-5 flex-grow-1 d-flex align-items-center">
                <div class="w-100">
                  <a class="link-fx fw-semibold fs-2 text-white" href="/">
                    AutomateHub
                  </a>
                  <p class="text-white-75 me-xl-8 mt-2">
                    Bienvenue sur AutomateHub. Connectez-vous pour accéder à votre espace et gérer vos projets d'automatisation n8n.
                  </p>
                </div>
              </div>
              <div class="p-4 p-xl-5 d-xl-flex justify-content-between align-items-center fs-sm">
                <p class="fw-medium text-white-50 mb-0">
                  <strong>AutomateHub</strong> &copy; {{ date('Y') }}
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
                <a class="link-fx fw-semibold fs-3 text-dark" href="/">
                  AutomateHub
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
                      Connexion
                    </h1>
                    <p class="fw-medium text-muted">
                      Bienvenue, veuillez vous connecter ou <a href="{{ route('register') }}">créer un compte</a>.
                    </p>
                  </div>
                  <!-- END Header -->

                  <!-- Sign In Form -->
                  <div class="row g-0 justify-content-center">
                    <div class="col-sm-8 col-xl-4">
                      <!-- Status Message -->
                      @if (session('status'))
                          <div class="alert alert-success mb-4">
                              <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                          </div>
                      @endif
                      
                      <form class="js-validation-signin" action="{{ route('login') }}" method="POST">
                          @csrf
                          
                          <!-- Email -->
                          <div class="mb-4">
                              <input type="email" 
                                     class="form-control form-control-lg form-control-alt py-3" 
                                     id="email" 
                                     name="email" 
                                     placeholder="Votre adresse email" 
                                     value="{{ old('email') }}" 
                                     required 
                                     autofocus>
                              @error('email')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Password -->
                          <div class="mb-4">
                              <div class="position-relative">
                                  <input type="password" 
                                         class="form-control form-control-lg form-control-alt py-3 pe-5" 
                                         id="password" 
                                         name="password" 
                                         placeholder="Votre mot de passe" 
                                         required>
                                  <button type="button" 
                                          class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 p-0" 
                                          onclick="togglePassword('password')"
                                          style="border: none; background: none; z-index: 10;">
                                      <i class="fas fa-eye text-muted" id="password-eye"></i>
                                  </button>
                              </div>
                              @error('password')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Remember Me & Submit -->
                          <div class="d-flex justify-content-between align-items-center mb-4">
                              <div>
                                  <div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                      <label class="form-check-label" for="remember">
                                          Se souvenir de moi
                                      </label>
                                  </div>
                                  @if (Route::has('password.request'))
                                      <a class="text-muted fs-sm fw-medium d-block d-lg-inline-block mb-1" href="{{ route('password.request') }}">
                                          Mot de passe oublié ?
                                      </a>
                                  @endif
                              </div>
                              <div>
                                  <button type="submit" class="btn btn-lg btn-alt-primary">
                                      <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i> Se connecter
                                  </button>
                              </div>
                          </div>
                      </form>
                      
                      <!-- Divider -->
                      <div class="text-center my-4">
                        <div class="d-flex align-items-center">
                          <hr class="flex-grow-1">
                          <span class="px-3 text-muted">ou</span>
                          <hr class="flex-grow-1">
                        </div>
                      </div>
                      
                      <!-- Google Login -->
                      <div class="text-center">
                        <a href="{{ route('auth.google') }}" class="btn btn-lg btn-alt-secondary w-100">
                          <i class="fab fa-google me-2"></i> Se connecter avec Google
                        </a>
                      </div>
                    </div>
                  </div>
                  <!-- END Sign In Form -->
                </div>
              </div>
              <div class="px-4 py-3 w-100 d-lg-none d-flex flex-column flex-sm-row justify-content-between fs-sm text-center text-sm-start">
                <p class="fw-medium text-black-50 py-2 mb-0">
                  <strong>AutomateHub</strong> &copy; {{ date('Y') }}
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
    <script>
    // Fonction pour basculer l'affichage du mot de passe
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(inputId + '-eye');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    jQuery(function () {
        // Validation du formulaire
        jQuery('.js-validation-signin').validate({
            rules: {
                'email': {
                    required: true,
                    email: true
                },
                'password': {
                    required: true,
                    minlength: 8
                }
            },
            messages: {
                'email': {
                    required: 'Veuillez entrer votre adresse email',
                    email: 'Veuillez entrer une adresse email valide'
                },
                'password': {
                    required: 'Veuillez entrer votre mot de passe',
                    minlength: 'Votre mot de passe doit contenir au moins 8 caractères'
                }
            },
            errorElement: 'div',
            errorClass: 'text-danger mt-2',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            highlight: function(element) {
                jQuery(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                jQuery(element).removeClass('is-invalid');
            }
        });
    });
    </script>
  </body>
</html>
