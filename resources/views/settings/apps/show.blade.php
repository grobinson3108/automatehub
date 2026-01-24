@extends('layouts.app')

@section('title', $app->name . ' Settings')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('settings.apps.index') }}" class="text-blue-600 hover:underline">← Back to My Apps</a>
    </div>

    <h1 class="text-3xl font-bold mb-2">{{ $app->name }} Settings</h1>
    <p class="text-gray-600 mb-8">{{ $app->description }}</p>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Subscription Info -->
    <div class="bg-white border rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Subscription</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Plan</p>
                <p class="font-semibold">{{ $subscription->pricingPlan->name }}</p>
            </div>
            <div>
                <p class="text-gray-600">Status</p>
                <p class="font-semibold">{{ ucfirst($subscription->status) }}</p>
            </div>
            <div>
                <p class="text-gray-600">Billing Period</p>
                <p class="font-semibold">{{ ucfirst($subscription->billing_period) }}</p>
            </div>
            @if($subscription->trial_ends_at)
            <div>
                <p class="text-gray-600">Trial Ends</p>
                <p class="font-semibold">{{ $subscription->trial_ends_at->format('M d, Y') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Credentials Configuration -->
    <div class="bg-white border rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Integrations & Credentials</h2>

        @if(empty($required_integrations))
            <p class="text-gray-600">This app doesn't require any external integrations.</p>
        @else
            <div class="space-y-4">
                @foreach($required_integrations as $service)
                    @php
                        $serviceCredentials = $credentials[$service] ?? null;
                        $isConfigured = $serviceCredentials !== null;
                    @endphp

                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold capitalize">{{ str_replace('_', ' ', $service) }}</h3>
                            @if($isConfigured)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Connected</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Not Connected</span>
                            @endif
                        </div>

                        @if($isConfigured)
                            <div class="text-sm text-gray-600 mb-3">
                                <p>Last verified: {{ $serviceCredentials['last_verified_at'] ? \Carbon\Carbon::parse($serviceCredentials['last_verified_at'])->diffForHumans() : 'Never' }}</p>
                                @if($serviceCredentials['expires_at'])
                                    <p>Expires: {{ \Carbon\Carbon::parse($serviceCredentials['expires_at'])->format('M d, Y') }}</p>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <form action="{{ route('settings.apps.credentials.verify', [$app, $service]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">
                                        Verify Connection
                                    </button>
                                </form>

                                <form action="{{ route('settings.apps.credentials.delete', [$app, $service]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to remove these credentials?')">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            <button onclick="alert('OAuth connection flow coming soon! For now, use API key form below.')" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">
                                Connect {{ ucfirst($service) }}
                            </button>

                            <!-- Simple API Key Form (for testing) -->
                            <details class="mt-3">
                                <summary class="cursor-pointer text-sm text-gray-600">Or enter API key manually</summary>
                                <form action="{{ route('settings.apps.credentials.store', $app) }}" method="POST" class="mt-3 space-y-2">
                                    @csrf
                                    <input type="hidden" name="service" value="{{ $service }}">
                                    <input type="hidden" name="type" value="api_key">

                                    <input type="text" name="credentials[api_key]" placeholder="Enter your API key" class="w-full border rounded px-3 py-2 text-sm" required>

                                    <button type="submit" class="bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">
                                        Save API Key
                                    </button>
                                </form>
                            </details>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- App Dashboard Link -->
    <div class="mt-6 text-center">
        <a href="{{ route('my-apps.dashboard', $app) }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
            Go to {{ $app->name }} Dashboard
        </a>
    </div>
</div>
@endsection
