<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name', 'AutomateHub') }} - Créer un compte</title>

    <meta name="description" content="Rejoignez AutomateHub et commencez votre parcours d'automatisation n8n">
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
                    La création d'un compte est entièrement gratuite. Accédez à nos tutoriels et ressources pour maîtriser n8n.
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
                      Créer un compte
                    </h1>
                    <p class="fw-medium text-muted">
                      Rejoignez AutomateHub et commencez votre parcours d'automatisation n8n
                    </p>
                  </div>
                  <!-- END Header -->

                  <!-- Sign Up Form -->
                  <div class="row g-0 justify-content-center">
                    <div class="col-sm-8 col-xl-4">
                      <!-- Affichage des erreurs générales -->
                      @if ($errors->any())
                          <div class="alert alert-danger mb-4">
                              <ul class="mb-0">
                                  @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                  @endforeach
                              </ul>
                          </div>
                      @endif
                      
                      <form class="js-validation-signup" action="{{ route('register') }}" method="POST">
                          @csrf
                          
                          <!-- Prénom -->
                          <div class="mb-4">
                              <input type="text" 
                                     class="form-control form-control-lg form-control-alt py-3" 
                                     id="first_name" 
                                     name="first_name" 
                                     placeholder="Prénom" 
                                     value="{{ old('first_name') }}" 
                                     required 
                                     autofocus>
                              @error('first_name')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Nom -->
                          <div class="mb-4">
                              <input type="text" 
                                     class="form-control form-control-lg form-control-alt py-3" 
                                     id="last_name" 
                                     name="last_name" 
                                     placeholder="Nom" 
                                     value="{{ old('last_name') }}" 
                                     required>
                              @error('last_name')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Nom d'utilisateur -->
                          <div class="mb-4">
                              <input type="text" 
                                     class="form-control form-control-lg form-control-alt py-3" 
                                     id="username" 
                                     name="username" 
                                     placeholder="Nom d'utilisateur" 
                                     value="{{ old('username') }}" 
                                     required>
                              <div class="username-suggestion" id="username-suggestion" style="font-size: 0.875rem; color: #6c757d; margin-top: 0.25rem;"></div>
                              @error('username')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Email -->
                          <div class="mb-4">
                              <input type="email" 
                                     class="form-control form-control-lg form-control-alt py-3" 
                                     id="email" 
                                     name="email" 
                                     placeholder="Adresse email" 
                                     value="{{ old('email') }}" 
                                     required>
                              @error('email')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Mot de passe -->
                          <div class="mb-4">
                              <div class="position-relative">
                                  <input type="password" 
                                         class="form-control form-control-lg form-control-alt py-3 pe-5" 
                                         id="password" 
                                         name="password" 
                                         placeholder="Mot de passe" 
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
                          
                          <!-- Confirmation mot de passe -->
                          <div class="mb-4">
                              <div class="position-relative">
                                  <input type="password" 
                                         class="form-control form-control-lg form-control-alt py-3 pe-5" 
                                         id="password_confirmation" 
                                         name="password_confirmation" 
                                         placeholder="Confirmer le mot de passe" 
                                         required>
                                  <button type="button" 
                                          class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 p-0" 
                                          onclick="togglePassword('password_confirmation')"
                                          style="border: none; background: none; z-index: 10;">
                                      <i class="fas fa-eye text-muted" id="password_confirmation-eye"></i>
                                  </button>
                              </div>
                          </div>
                          
                          <!-- Cases à cocher -->
                          <div class="mb-4">
                              <div class="d-md-flex align-items-md-center justify-content-md-between">
                                  <!-- Case Professionnel -->
                                  <div class="form-check">
                                      <input class="form-check-input" 
                                             type="checkbox" 
                                             id="is_professional" 
                                             name="is_professional" 
                                             value="1" 
                                             {{ old('is_professional') ? 'checked' : '' }}>
                                      <label class="form-check-label" for="is_professional">
                                          Je suis un professionnel
                                      </label>
                                  </div>
                              </div>
                              
                              <!-- Case RGPD -->
                              <div class="d-md-flex align-items-md-center justify-content-md-between mt-2">
                                  <div class="form-check">
                                      <input class="form-check-input" 
                                             type="checkbox" 
                                             id="rgpd_accepted" 
                                             name="rgpd_accepted" 
                                             value="1" 
                                             required>
                                      <label class="form-check-label" for="rgpd_accepted">
                                          J'accepte les conditions générales
                                      </label>
                                  </div>
                                  <div class="py-2">
                                      <a class="fs-sm fw-medium" href="#" data-bs-toggle="modal" data-bs-target="#one-signup-terms">Voir les conditions</a>
                                  </div>
                              </div>
                              @error('rgpd_accepted')
                                  <div class="text-danger mt-2">{{ $message }}</div>
                              @enderror
                          </div>
                          
                          <!-- Bouton de soumission -->
                          <div class="text-center">
                              <button type="submit" class="btn btn-lg btn-alt-success">
                                  <i class="fa fa-fw fa-plus me-1 opacity-50"></i> Créer mon compte
                              </button>
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
                      
                      <!-- Google Sign Up -->
                      <div class="text-center">
                        <a href="{{ route('auth.google') }}" class="btn btn-lg btn-alt-secondary w-100">
                          <i class="fab fa-google me-2"></i> S'inscrire avec Google
                        </a>
                        <p class="mt-3 mb-0">
                          <small class="text-muted">
                            Déjà inscrit ? 
                            <a href="{{ route('login') }}" class="fw-medium">Se connecter</a>
                          </small>
                        </p>
                      </div>
                    </div>
                  </div>
                  <!-- END Sign Up Form -->
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

          <!-- Terms Modal -->
          <div class="modal fade" id="one-signup-terms" tabindex="-1" role="dialog" aria-labelledby="one-signup-terms" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
              <div class="modal-content">
                <div class="block block-rounded block-transparent mb-0">
                  <div class="block-header block-header-default">
                    <h3 class="block-title">Conditions générales</h3>
                    <div class="block-options">
                      <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-fw fa-times"></i>
                      </button>
                    </div>
                  </div>
                  <div class="block-content">
                    <p>En créant un compte sur AutomateHub, vous acceptez nos conditions d'utilisation et notre politique de confidentialité.</p>
                    <p>Votre compte vous donne accès à nos tutoriels gratuits et payants sur l'automatisation avec n8n.</p>
                    <p>Nous nous engageons à protéger vos données personnelles conformément au RGPD.</p>
                  </div>
                  <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">J'accepte</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- END Terms Modal -->
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
        // Génération automatique du nom d'utilisateur
        function generateUsername() {
            const firstName = jQuery('#first_name').val().toLowerCase().trim();
            const lastName = jQuery('#last_name').val().toLowerCase().trim();
            
            if (firstName && lastName) {
                // Supprimer les accents et caractères spéciaux
                const cleanFirstName = firstName.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-z]/g, "");
                const cleanLastName = lastName.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-z]/g, "");
                
                if (cleanFirstName && cleanLastName) {
                    const suggestions = [
                        cleanFirstName + '.' + cleanLastName,
                        cleanFirstName + cleanLastName,
                        cleanFirstName + '_' + cleanLastName,
                        cleanLastName + '.' + cleanFirstName
                    ];
                    
                    const suggestion = suggestions[0];
                    jQuery('#username').val(suggestion);
                    jQuery('#username-suggestion').html('<i class="fas fa-lightbulb me-1"></i>Suggestion: ' + suggestion);
                }
            }
        }
        
        // Générer le nom d'utilisateur quand on tape le nom/prénom
        jQuery('#first_name, #last_name').on('input', generateUsername);
        
        // Validation du formulaire OneUI
        jQuery('.js-validation-signup').validate({
            rules: {
                'first_name': {
                    required: true,
                    minlength: 2
                },
                'last_name': {
                    required: true,
                    minlength: 2
                },
                'username': {
                    required: true,
                    minlength: 3
                },
                'email': {
                    required: true,
                    email: true
                },
                'password': {
                    required: true,
                    minlength: 8
                },
                'password_confirmation': {
                    required: true,
                    equalTo: '#password'
                },
                'rgpd_accepted': {
                    required: true
                }
            },
            messages: {
                'first_name': {
                    required: 'Veuillez entrer votre prénom',
                    minlength: 'Votre prénom doit contenir au moins 2 caractères'
                },
                'last_name': {
                    required: 'Veuillez entrer votre nom',
                    minlength: 'Votre nom doit contenir au moins 2 caractères'
                },
                'username': {
                    required: 'Veuillez entrer un nom d\'utilisateur',
                    minlength: 'Votre nom d\'utilisateur doit contenir au moins 3 caractères'
                },
                'email': {
                    required: 'Veuillez entrer votre adresse email',
                    email: 'Veuillez entrer une adresse email valide'
                },
                'password': {
                    required: 'Veuillez entrer un mot de passe',
                    minlength: 'Votre mot de passe doit contenir au moins 8 caractères'
                },
                'password_confirmation': {
                    required: 'Veuillez confirmer votre mot de passe',
                    equalTo: 'Les mots de passe ne correspondent pas'
                },
                'rgpd_accepted': {
                    required: 'Vous devez accepter les conditions générales'
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
        
        // Générer le nom d'utilisateur initial si les champs sont pré-remplis
        generateUsername();
    });
    </script>
  </body>
</html>
