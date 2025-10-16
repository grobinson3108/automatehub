@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Tableau de bord API</h1>
        
        {{-- Statistiques globales --}}
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">APIs actives</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $subscriptions->count() }}</p>
                    </div>
                    <i class="fas fa-plug text-3xl text-blue-200"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Crédits utilisés</p>
                        <p class="text-3xl font-bold text-orange-600">{{ number_format($totalCreditsUsed) }}</p>
                    </div>
                    <i class="fas fa-chart-line text-3xl text-orange-200"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Crédits restants</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($totalCreditsAvailable) }}</p>
                    </div>
                    <i class="fas fa-coins text-3xl text-green-200"></i>
                </div>
            </div>
        </div>
        
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Vos souscriptions --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Vos souscriptions API</h2>
                    
                    @if($subscriptions->isEmpty())
                    <p class="text-gray-600">Vous n'avez pas encore de souscription active.</p>
                    <a href="{{ route('api-marketplace.index') }}" 
                       class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Découvrir les APIs
                    </a>
                    @else
                    <div class="space-y-4">
                        @foreach($subscriptions as $subscription)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        @if($subscription->apiService->icon)
                                        <i class="{{ $subscription->apiService->icon }} text-2xl text-blue-600 mr-3"></i>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold">{{ $subscription->apiService->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $subscription->pricingPlan->name }}</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Clé API --}}
                                    <div class="mb-3">
                                        <p class="text-xs text-gray-500 mb-1">Clé API</p>
                                        <div class="flex items-center">
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ substr($subscription->api_key, 0, 20) }}...</code>
                                            <button onclick="copyKey('{{ $subscription->api_key }}')" 
                                                    class="ml-2 text-xs text-blue-600 hover:underline">
                                                Copier
                                            </button>
                                        </div>
                                    </div>
                                    
                                    {{-- Barre de progression --}}
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-xs text-gray-600">Utilisation</span>
                                            <span class="text-xs font-semibold">
                                                {{ $subscription->used_this_month }} / {{ $subscription->monthly_quota + $subscription->extra_credits }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            @php
                                                $percentage = $subscription->monthly_quota + $subscription->extra_credits > 0 
                                                    ? min(100, ($subscription->used_this_month / ($subscription->monthly_quota + $subscription->extra_credits)) * 100)
                                                    : 0;
                                            @endphp
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Renouvellement : {{ $subscription->reset_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="ml-4">
                                    <a href="{{ route('api-marketplace.show', $subscription->apiService->slug) }}" 
                                       class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                        Gérer
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Activité récente --}}
            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Activité récente</h2>
                    
                    @if($recentActivity->isEmpty())
                    <p class="text-gray-600">Aucune activité récente.</p>
                    @else
                    <div class="space-y-3">
                        @foreach($recentActivity as $log)
                        <div class="border-b pb-3 last:border-b-0 last:pb-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium">{{ $log->subscription->apiService->name }}</p>
                                    <p class="text-xs text-gray-600">
                                        {{ $log->method }} {{ $log->endpoint }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($log->wasSuccessful())
                                    <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                        {{ $log->response_code }}
                                    </span>
                                    @else
                                    <span class="inline-block px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                        {{ $log->response_code ?? 'Erreur' }}
                                    </span>
                                    @endif
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ $log->credits_used }} crédit{{ $log->credits_used > 1 ? 's' : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                {{-- Actions rapides --}}
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>
                    
                    <div class="space-y-3">
                        <a href="{{ route('api-marketplace.index') }}" 
                           class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Découvrir d'autres APIs
                        </a>
                        
                        <a href="https://n8n.automatehub.fr" 
                           target="_blank"
                           class="block w-full px-4 py-3 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Ouvrir n8n
                        </a>
                        
                        <a href="#" 
                           class="block w-full px-4 py-3 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200">
                            <i class="fas fa-book mr-2"></i>
                            Documentation API
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyKey(key) {
    const textArea = document.createElement('textarea');
    textArea.value = key;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    
    // Feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copié !';
    
    setTimeout(() => {
        button.textContent = originalText;
    }, 2000);
}
</script>
@endsection