@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Content Extractor - Tableau de bord</h1>
        
        {{-- Statut du compte --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Votre compte</h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Type d'abonnement</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ ucfirst($account->subscription_type) }}
                        @if($account->subscription_type === 'skool')
                            <span class="text-sm text-green-600">(via Skool)</span>
                        @endif
                    </p>
                </div>
                
                <div>
                    <p class="text-gray-600">Crédits ce mois</p>
                    <p class="text-2xl font-bold">
                        {{ $account->used_this_month }} / {{ $account->monthly_quota }}
                        @if($account->extra_credits > 0)
                            <span class="text-sm text-green-600">(+{{ $account->extra_credits }} extras)</span>
                        @endif
                    </p>
                </div>
            </div>
            
            {{-- Barre de progression --}}
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full" 
                         style="width: {{ min(100, ($account->used_this_month / $account->monthly_quota) * 100) }}%">
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Clé API --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Votre clé API</h2>
            
            <div class="flex items-center space-x-2">
                <input type="text" 
                       value="{{ $apiKey }}" 
                       readonly
                       class="flex-1 px-4 py-2 border rounded-lg bg-gray-50"
                       id="apiKey">
                <button onclick="copyApiKey()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Copier
                </button>
            </div>
            
            <p class="mt-2 text-sm text-gray-600">
                Utilisez cette clé dans le node n8n Content Extractor
            </p>
        </div>
        
        {{-- Acheter des crédits --}}
        @if($account->subscription_type !== 'unlimited')
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Acheter des crédits supplémentaires</h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                @foreach($stripeLinks as $pack)
                <div class="border rounded-lg p-4 text-center hover:shadow-lg transition">
                    <h3 class="font-semibold text-lg">{{ $pack['name'] }}</h3>
                    <p class="text-3xl font-bold my-4">{{ $pack['price'] }}</p>
                    <a href="{{ $pack['link'] }}?prefilled_email={{ auth()->user()->email }}" 
                       class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Acheter
                    </a>
                </div>
                @endforeach
            </div>
            
            @if($account->subscription_type === 'free')
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-800">
                    💡 <strong>Astuce :</strong> Rejoignez la communauté Skool pour obtenir 100 crédits/mois inclus !
                    <a href="https://www.skool.com/automatehub" class="underline">En savoir plus →</a>
                </p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
function copyApiKey() {
    const input = document.getElementById('apiKey');
    input.select();
    document.execCommand('copy');
    
    // Feedback visuel
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copié !';
    button.classList.add('bg-green-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
    }, 2000);
}
</script>
@endsection