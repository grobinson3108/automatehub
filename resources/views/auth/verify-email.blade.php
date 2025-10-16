@extends('auth.layouts.auth')

@section('title', 'Vérification de l\'email')
@section('description', 'Veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.')
@section('bg-class', 'bg-primary')
@section('sidebar_description', 'Pour sécuriser votre compte et accéder à toutes les fonctionnalités, nous avons besoin de vérifier votre adresse email.')

@section('content')
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4">
            Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.
        </div>
    @endif

    <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        <div class="mb-4">
            <p class="text-center">
                Si vous n'avez pas reçu l'email, vous pouvez demander un nouveau lien de vérification en cliquant sur le bouton ci-dessous.
            </p>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-lg btn-alt-primary">
                <i class="fa fa-fw fa-envelope me-1 opacity-50"></i> Renvoyer l'email de vérification
            </button>
        </div>
        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-muted fs-sm fw-medium btn btn-link">
                    <i class="fa fa-sign-out-alt me-1"></i> Déconnexion
                </button>
            </form>
        </div>
    </form>
@endsection

@section('js_after')
<script>
    jQuery(function () {
        // Init any OneUI JS functionality if needed
    });
</script>
@endsection
