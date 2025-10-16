@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Marketplace API pour n8n</h1>
        
        <p class="text-lg text-gray-600 mb-8">
            Découvrez nos APIs spécialement conçues pour vos workflows n8n. 
            Simplifiez vos automatisations avec des services puissants et fiables.
        </p>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($apiServices as $api)
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <div class="p-6">
                    {{-- Icône --}}
                    @if($api->icon)
                    <div class="w-16 h-16 mb-4 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="{{ $api->icon }} text-2xl text-blue-600"></i>
                    </div>
                    @endif
                    
                    {{-- Titre et catégorie --}}
                    <h3 class="text-xl font-semibold mb-2">{{ $api->name }}</h3>
                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded mb-3">
                        {{ ucfirst($api->category) }}
                    </span>
                    
                    {{-- Description --}}
                    <p class="text-gray-600 mb-4">{{ $api->description }}</p>
                    
                    {{-- Fonctionnalités --}}
                    @if(count($api->features) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold mb-2">Fonctionnalités :</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach(array_slice($api->features, 0, 3) as $feature)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mr-1 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    {{-- Statut de souscription --}}
                    @php
                        $subscription = $userSubscriptions->get($api->id);
                    @endphp
                    
                    @if($subscription)
                    <div class="mt-4 p-3 bg-green-50 rounded-lg">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            {{ $subscription->pricingPlan->name }}
                            - {{ $subscription->remaining_credits }} crédits restants
                        </p>
                    </div>
                    @else
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">À partir de</p>
                        <p class="text-2xl font-bold text-blue-600">
                            @if($api->pricingPlans->where('monthly_price', 0)->isNotEmpty())
                                Gratuit
                            @else
                                {{ number_format($api->pricingPlans->min('monthly_price'), 2) }}€/mois
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    {{-- Bouton d'action --}}
                    <a href="{{ route('api-marketplace.show', $api->slug) }}" 
                       class="block w-full mt-4 px-4 py-2 text-center rounded-lg transition
                              @if($subscription)
                                bg-blue-600 text-white hover:bg-blue-700
                              @else
                                bg-gray-900 text-white hover:bg-gray-800
                              @endif">
                        @if($subscription)
                            Gérer
                        @else
                            Découvrir
                        @endif
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Dashboard link --}}
        @auth
        <div class="mt-12 text-center">
            <a href="{{ route('api-marketplace.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Voir mon tableau de bord API
            </a>
        </div>
        @endauth
    </div>
</div>
@endsection