@extends('layouts.backend')

@section('title', 'Test Traduction - Automatehub Admin')

@section('content')
<div class="content">
  <h1>Test Page de Traduction</h1>
  <p>Si vous voyez ce message, la page se charge correctement.</p>

  <div class="alert alert-info">
    <strong>Test de base :</strong> Cette page fonctionne !
  </div>

  <div id="status-test">
    <p>Vérification du statut...</p>
  </div>
</div>
@endsection

@section('js_after')
<script>
console.log('JavaScript chargé correctement');

// Test de l'API de statut
fetch('{{ route("admin.tools.workflow-translation.status") }}')
    .then(response => {
        console.log('Réponse reçue:', response);
        return response.json();
    })
    .then(data => {
        console.log('Données:', data);
        document.getElementById('status-test').innerHTML =
            '<div class="alert alert-success">API fonctionne : ' + JSON.stringify(data) + '</div>';
    })
    .catch(error => {
        console.error('Erreur API:', error);
        document.getElementById('status-test').innerHTML =
            '<div class="alert alert-danger">Erreur API : ' + error.message + '</div>';
    });
</script>
@endsection