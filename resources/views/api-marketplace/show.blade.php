@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('api-marketplace.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
                ← Retour au marketplace
            </a>
            
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $apiService->name }}</h1>
                    <p class="text-lg text-gray-600">{{ $apiService->description }}</p>
                </div>
                
                @if($apiService->icon)
                <div class="w-20 h-20 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="{{ $apiService->icon }} text-3xl text-blue-600"></i>
                </div>
                @endif
            </div>
        </div>
        
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Fonctionnalités --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Fonctionnalités</h2>
                    <ul class="space-y-2">
                        @foreach($apiService->features as $feature)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                {{-- Statistiques d'utilisation --}}
                @if($subscription && $usageStats)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Statistiques d'utilisation</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $usageStats['today'] }}</p>
                            <p class="text-sm text-gray-600">Aujourd'hui</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $usageStats['this_week'] }}</p>
                            <p class="text-sm text-gray-600">Cette semaine</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $usageStats['this_month'] }}</p>
                            <p class="text-sm text-gray-600">Ce mois</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $usageStats['success_rate'] }}%</p>
                            <p class="text-sm text-gray-600">Taux de succès</p>
                        </div>
                    </div>
                    
                    {{-- Barre de progression --}}
                    <div class="mt-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600">Quota mensuel</span>
                            <span class="text-sm font-semibold">
                                {{ $subscription->used_this_month }} / {{ $subscription->monthly_quota + $subscription->extra_credits }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            @php
                                $percentage = min(100, ($subscription->used_this_month / ($subscription->monthly_quota + $subscription->extra_credits)) * 100);
                            @endphp
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- Documentation --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Comment utiliser dans n8n</h2>
                    
                    @if($subscription)
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold mb-2">1. Installez le node communautaire</h3>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-sm">{{ $apiService->node_package ?? 'n8n-nodes-' . $apiService->slug }}</code>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-semibold mb-2">2. Configurez vos identifiants</h3>
                            <p class="text-gray-600 mb-2">Utilisez votre clé API dans les paramètres du node :</p>
                            <div class="bg-gray-50 p-3 rounded flex items-center justify-between">
                                <code class="text-sm" id="apiKey">{{ $subscription->api_key }}</code>
                                <button onclick="copyApiKey()" class="ml-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    Copier
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-semibold mb-2">3. Endpoint de l'API</h3>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-sm">{{ url('/api/proxy/' . $apiService->slug) }}</code>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-600">
                        Souscrivez à un plan pour obtenir votre clé API et commencer à utiliser ce service dans vos workflows n8n.
                    </p>
                    @endif
                </div>
            </div>
            
            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Souscription actuelle --}}
                @if($subscription)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Votre souscription</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Plan actuel</p>
                            <p class="font-semibold">{{ $subscription->pricingPlan->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Crédits restants</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $subscription->remaining_credits }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Renouvellement</p>
                            <p class="font-semibold">{{ $subscription->reset_date->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($subscription->isTrialing())
                        <div class="p-3 bg-yellow-50 rounded">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Période d'essai jusqu'au {{ $subscription->trial_ends_at->format('d/m/Y') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                
                {{-- Acheter des crédits --}}
                @if($creditPacks->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Acheter des crédits</h3>
                    
                    <div class="space-y-3">
                        @foreach($creditPacks as $pack)
                        <div class="border rounded-lg p-4 hover:shadow transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-semibold">{{ $pack->name }}</h4>
                                    <p class="text-gray-600">{{ $pack->credits }} crédits</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold">{{ $pack->display_price }}</p>
                                    @if($pack->discount_percentage > 0)
                                    <p class="text-sm text-green-600">-{{ $pack->discount_percentage }}%</p>
                                    @endif
                                </div>
                            </div>
                            <button class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
                                    onclick="buyCredits('{{ $pack->id }}')">
                                Acheter
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                @else
                {{-- Plans tarifaires --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Choisir un plan</h3>
                    
                    <div class="space-y-4">
                        @foreach($apiService->pricingPlans as $plan)
                        <div class="border rounded-lg p-4 {{ $plan->isFree() ? 'border-gray-300' : 'border-blue-500' }}">
                            <h4 class="font-semibold mb-2">{{ $plan->name }}</h4>
                            
                            <p class="text-2xl font-bold mb-2">
                                {{ $plan->display_price }}
                            </p>
                            
                            <p class="text-sm text-gray-600 mb-3">
                                {{ $plan->monthly_quota }} requêtes/mois
                            </p>
                            
                            @if($plan->features)
                            <ul class="text-sm text-gray-600 space-y-1 mb-4">
                                @foreach($plan->features as $feature)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-1 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            
                            <form action="{{ route('api-marketplace.subscribe', $apiService->slug) }}" method="POST">
                                @csrf
                                <input type="hidden" name="pricing_plan_id" value="{{ $plan->id }}">
                                <button type="submit" 
                                        class="w-full px-4 py-2 rounded transition
                                               {{ $plan->isFree() ? 'bg-gray-900 text-white hover:bg-gray-800' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                    {{ $plan->isFree() ? 'Commencer gratuitement' : 'Souscrire' }}
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyApiKey() {
    const apiKeyElement = document.getElementById('apiKey');
    const textArea = document.createElement('textarea');
    textArea.value = apiKeyElement.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    
    // Feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copié !';
    button.classList.add('bg-green-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
    }, 2000);
}

function buyCredits(packId) {
    // TODO: Implémenter l'achat de crédits
    console.log('Acheter pack:', packId);
}
</script>
@endsection