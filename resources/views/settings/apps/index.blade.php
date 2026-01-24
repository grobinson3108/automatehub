@extends('layouts.app')

@section('title', 'My Apps Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">My Apps Settings</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($apps->isEmpty())
        <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 text-center">
            <p class="text-gray-600">You don't have any active apps yet.</p>
            <a href="{{ route('apps.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Browse Apps
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($apps as $appData)
                @php
                    $app = $appData['app'];
                    $subscription = $appData['subscription'];
                    $isConfigured = $appData['is_configured'];
                @endphp

                <div class="bg-white border rounded-lg p-6 shadow hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">{{ $app->name }}</h3>
                        @if($isConfigured)
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Configured</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Setup Required</span>
                        @endif
                    </div>

                    <p class="text-gray-600 text-sm mb-4">{{ $app->tagline ?? $app->description }}</p>

                    <div class="text-sm text-gray-500 mb-4">
                        <p><strong>Plan:</strong> {{ $subscription->pricingPlan->name }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($subscription->status) }}</p>
                    </div>

                    <a href="{{ route('settings.apps.show', $app) }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Configure
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
