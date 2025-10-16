@extends('auth.layouts.auth')

@section('title', 'Réinitialisation du mot de passe')
@section('description', 'Veuillez entrer votre nouveau mot de passe ci-dessous.')
@section('bg-class', 'bg-primary')
@section('sidebar_description', 'Vous êtes sur le point de réinitialiser votre mot de passe. Choisissez un mot de passe fort pour sécuriser votre compte.')

@section('content')
    <!-- Reset Password Form -->
    <form class="js-validation-reset" action="{{ route('password.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <input type="email" class="form-control form-control-lg form-control-alt py-3" id="email" name="email" value="{{ $request->email }}" readonly>
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <input type="password" class="form-control form-control-lg form-control-alt py-3" id="password" name="password" placeholder="Nouveau mot de passe" required autofocus>
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <input type="password" class="form-control form-control-lg form-control-alt py-3" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
        </div>
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-alt-primary">
                <i class="fa fa-fw fa-save me-1 opacity-50"></i> Réinitialiser le mot de passe
            </button>
        </div>
    </form>
    <!-- END Reset Password Form -->
@endsection

@section('js_after')
<script>
    jQuery(function () {
        // Init Form Validation
        One.helpers('jq-validation');

        // Init Validation on Reset Password form
        jQuery('.js-validation-reset').validate({
            rules: {
                'password': {
                    required: true,
                    minlength: 8
                },
                'password_confirmation': {
                    required: true,
                    equalTo: '#password'
                }
            },
            messages: {
                'password': {
                    required: 'Veuillez entrer un nouveau mot de passe',
                    minlength: 'Votre mot de passe doit contenir au moins 8 caractères'
                },
                'password_confirmation': {
                    required: 'Veuillez confirmer votre mot de passe',
                    equalTo: 'Les mots de passe ne correspondent pas'
                }
            }
        });
    });
</script>
@endsection
