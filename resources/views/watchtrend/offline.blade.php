@extends('watchtrend.layouts.app')

@section('title', 'Hors ligne')

@section('page-title', 'Hors ligne')

@section('breadcrumb')
  <li class="breadcrumb-item active" aria-current="page">Hors ligne</li>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="block block-rounded text-center py-5">
      <div class="block-content">
        <div class="mb-4">
          <i class="fa fa-plug-circle-xmark fa-4x text-muted" style="opacity: 0.4;"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Vous êtes hors ligne</h2>
        <p class="text-muted mb-4">
          Impossible de se connecter à WatchTrend.<br>
          Vérifiez votre connexion internet et réessayez.
        </p>
        <button type="button" class="btn btn-primary" onclick="location.reload()">
          <i class="fa fa-redo me-2"></i>Réessayer
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
