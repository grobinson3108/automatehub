@extends('layouts.app')

@section('title', 'Dashboard Système - AutomateHub Admin')
@section('description', 'Dashboard de surveillance du système AutomateHub - Monitoring des performances, sécurité et état des services.')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        Dashboard Système
                    </h1>
                    <p class="text-muted mb-0">Surveillance et monitoring AutomateHub</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statut global -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        @php
                            $overallStatus = 'good';
                            if (isset($securityStatus['overall']) && $securityStatus['overall'] === 'critical') {
                                $overallStatus = 'critical';
                            } elseif (isset($backupStatus['overall']) && $backupStatus['overall'] === 'warning') {
                                $overallStatus = 'warning';
                            }
                        @endphp
                        @if($overallStatus === 'good')
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        @elseif($overallStatus === 'warning')
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        @else
                            <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
                        @endif
                    </div>
                    <h5 class="card-title mb-1">Statut Global</h5>
                    <p class="card-text">
                        @if($overallStatus === 'good')
                            <span class="badge bg-success">Système Opérationnel</span>
                        @elseif($overallStatus === 'warning')
                            <span class="badge bg-warning">Attention Requise</span>
                        @else
                            <span class="badge bg-danger">Problème Critique</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-server text-info me-2"></i>
                        Serveur
                    </h6>
                    @if(isset($systemStats['server']))
                        <p class="mb-1"><small>PHP {{ $systemStats['server']['php_version'] }}</small></p>
                        <p class="mb-1"><small>Laravel {{ $systemStats['server']['laravel_version'] }}</small></p>
                        <p class="mb-0"><small>Uptime: {{ $systemStats['server']['uptime'] }}</small></p>
                    @else
                        <p class="text-muted">Données indisponibles</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-database text-primary me-2"></i>
                        Base de Données
                    </h6>
                    @if(isset($databaseStats['connection_status']))
                        @if($databaseStats['connection_status'] === 'connected')
                            <span class="badge bg-success">Connectée</span>
                            <p class="mb-0 mt-2"><small>{{ $databaseStats['total_tables'] }} tables</small></p>
                        @else
                            <span class="badge bg-danger">Déconnectée</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Inconnue</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        Sécurité
                    </h6>
                    @if(isset($securityStatus['score']))
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                <div class="progress-bar 
                                    @if($securityStatus['score'] >= 80) bg-success
                                    @elseif($securityStatus['score'] >= 60) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ $securityStatus['score'] }}%">
                                </div>
                            </div>
                            <small>{{ $securityStatus['score'] }}%</small>
                        </div>
                    @else
                        <span class="badge bg-secondary">Non évalué</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Métriques système -->
    <div class="row mb-4">
        <!-- Espace disque -->
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-hdd text-primary me-2"></i>
                    <h6 class="mb-0">Espace Disque</h6>
                </div>
                <div class="card-body">
                    @if(isset($systemStats['disk']))
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fs-6 fw-bold">{{ $systemStats['disk']['total'] }}</div>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fs-6 fw-bold">{{ $systemStats['disk']['used'] }}</div>
                                    <small class="text-muted">Utilisé</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="fs-6 fw-bold text-success">{{ $systemStats['disk']['free'] }}</div>
                                <small class="text-muted">Libre</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar 
                                    @if($systemStats['disk']['status'] === 'good') bg-success
                                    @elseif($systemStats['disk']['status'] === 'warning') bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ $systemStats['disk']['percent_used'] }}%">
                                </div>
                            </div>
                            <small class="text-muted">{{ $systemStats['disk']['percent_used'] }}% utilisé</small>
                        </div>
                    @else
                        <p class="text-muted">Données indisponibles</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mémoire -->
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-memory text-info me-2"></i>
                    <h6 class="mb-0">Mémoire</h6>
                </div>
                <div class="card-body">
                    @if(isset($systemStats['memory']) && isset($systemStats['memory']['system_memory']))
                        @php $mem = $systemStats['memory']['system_memory']; @endphp
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fs-6 fw-bold">{{ $mem['total_mb'] }}MB</div>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fs-6 fw-bold">{{ $mem['used_mb'] }}MB</div>
                                    <small class="text-muted">Utilisé</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="fs-6 fw-bold text-success">{{ $mem['available_mb'] }}MB</div>
                                <small class="text-muted">Libre</small>
                            </div>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar 
                                @if($mem['status'] === 'good') bg-success
                                @elseif($mem['status'] === 'warning') bg-warning
                                @else bg-danger
                                @endif" 
                                style="width: {{ $mem['percent_used'] }}%">
                            </div>
                        </div>
                        <small class="text-muted">{{ $mem['percent_used'] }}% utilisé</small>
                    @elseif(isset($systemStats['memory']))
                        <p><strong>PHP:</strong></p>
                        <p class="mb-1">Usage: {{ $systemStats['memory']['php_memory_usage'] ?? 'N/A' }}</p>
                        <p class="mb-1">Peak: {{ $systemStats['memory']['php_memory_peak'] ?? 'N/A' }}</p>
                        <p class="mb-0">Limite: {{ $systemStats['memory']['php_memory_limit'] ?? 'N/A' }}</p>
                    @else
                        <p class="text-muted">Données indisponibles</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Logs -->
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-file-alt text-warning me-2"></i>
                    <h6 class="mb-0">Logs Système</h6>
                </div>
                <div class="card-body">
                    @if(isset($logStats))
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="fs-6 fw-bold">{{ $logStats['file_count'] ?? 0 }}</div>
                                    <small class="text-muted">Fichiers</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fs-6 fw-bold">{{ $logStats['total_size'] ?? '0 B' }}</div>
                                <small class="text-muted">Taille</small>
                            </div>
                        </div>
                        @if(isset($logStats['recent_errors']) && $logStats['recent_errors'] > 0)
                            <div class="alert alert-warning alert-sm">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ $logStats['recent_errors'] }} erreur(s) récente(s)
                            </div>
                        @else
                            <div class="alert alert-success alert-sm">
                                <i class="fas fa-check me-1"></i>
                                Aucune erreur récente
                            </div>
                        @endif
                    @else
                        <p class="text-muted">Données indisponibles</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sécurité et Backups -->
    <div class="row mb-4">
        <!-- Statut sécurité -->
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-shield-alt text-success me-2"></i>
                    <h6 class="mb-0">Statut Sécurité</h6>
                </div>
                <div class="card-body">
                    @if(isset($securityStatus['checks']))
                        @foreach($securityStatus['checks'] as $check)
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    @if($check['status'] === 'good')
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                    @elseif($check['status'] === 'warning')
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $check['name'] }}</div>
                                        <small class="text-muted">{{ $check['details'] }}</small>
                                    </div>
                                </div>
                                <span class="badge 
                                    @if($check['status'] === 'good') bg-success
                                    @elseif($check['status'] === 'warning') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($check['status']) }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Évaluation de sécurité indisponible</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statut backups -->
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-database text-primary me-2"></i>
                    <h6 class="mb-0">Statut Backups</h6>
                </div>
                <div class="card-body">
                    @if(isset($backupStatus['overall']))
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span>Statut global:</span>
                                <span class="badge 
                                    @if($backupStatus['overall'] === 'good') bg-success
                                    @else bg-warning
                                    @endif">
                                    {{ $backupStatus['overall'] === 'good' ? 'OK' : 'Attention' }}
                                </span>
                            </div>
                            @if(isset($backupStatus['last_check']))
                                <small class="text-muted">Dernière vérification: {{ $backupStatus['last_check'] }}</small>
                            @endif
                        </div>

                        @if(isset($backupStatus['locations']))
                            @foreach($backupStatus['locations'] as $location)
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div class="fw-medium">{{ $location['description'] }}</div>
                                        <small class="text-muted">{{ $location['path'] }}</small>
                                    </div>
                                    @if($location['exists'])
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    @else
                        <p class="text-muted">Statut backup indisponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Base de données -->
    @if(isset($databaseStats['tables']) && !empty($databaseStats['tables']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-table text-primary me-2"></i>
                    <h6 class="mb-0">Tables Base de Données (Top 10)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Table</th>
                                    <th>Lignes</th>
                                    <th>Taille</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($databaseStats['tables'] as $table)
                                <tr>
                                    <td><code>{{ $table['name'] }}</code></td>
                                    <td>{{ number_format($table['rows']) }}</td>
                                    <td>{{ $table['size_mb'] }} MB</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-tachometer-alt text-info me-2"></i>
                    <h6 class="mb-0">Métriques de Performance</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($performanceMetrics['cache']))
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-layer-group text-primary me-2"></i>
                                <div>
                                    <div class="fw-medium">Cache</div>
                                    <span class="badge {{ $performanceMetrics['cache']['working'] ? 'bg-success' : 'bg-warning' }}">
                                        {{ $performanceMetrics['cache']['working'] ? 'Fonctionnel' : 'Problème' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($performanceMetrics['response_time']))
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <div>
                                    <div class="fw-medium">Temps de réponse</div>
                                    <small class="text-muted">{{ $performanceMetrics['response_time']['average_ms'] }}ms</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($performanceMetrics['queues']))
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-list text-info me-2"></i>
                                <div>
                                    <div class="fw-medium">Queues</div>
                                    <small class="text-muted">{{ $performanceMetrics['queues']['pending_jobs'] }} tâches en attente</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}
</style>

<script>
// Auto-refresh toutes les 5 minutes
setTimeout(function() {
    window.location.reload();
}, 300000);

// Indicateur de dernière mise à jour
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR');
    console.log('Dashboard mis à jour à:', timeString);
});
</script>
@endsection