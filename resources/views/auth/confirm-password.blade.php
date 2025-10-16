@extends('auth.layouts.auth')

@section('title', 'Confirmation du mot de passe')
@section('description', 'Ceci est une zone sécurisée de l\'application. Veuillez confirmer votre mot de passe avant de continuer.')
@section('bg-class', 'bg-primary')
@section('sidebar_description', 'Pour protéger vos données, nous avons besoin de vérifier votre identité. Veuillez confirmer votre mot de passe pour continuer.')

@section('content')
    <!-- Confirm Password Form -->
    <form class="js-validation-confirm" action="{{ route('password.confirm') }}" method="POST">
        @csrf
        <div class="mb-4">
            <input type="password" class="form-control form-control-lg form-control-alt py-3" id="password" name="password" placeholder="Mot de passe" required autofocus>
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-alt-primary">
                <i class="fa fa-fw fa-lock me-1 opacity-50"></i> Confirmer
            </button>
        </div>
    </form>
    <!-- END Confirm Password Form -->
@endsection

@section('js_after')
<script>
    jQuery(function () {
        // Init Form Validation
        One.helpers('jq-validation');

        // Init Validation on Confirm Password form
        jQuery('.js-validation-confirm').validate({
            rules: {
                'password': {
                    required: true,
                    minlength: 8
                }
            },
            messages: {
                'password': {
                    required: 'Veuillez entrer votre mot de passe',
                    minlength: 'Votre mot de passe doit contenir au moins 8 caractères'
                }
            }
        });
    });
</script>
@endsection
