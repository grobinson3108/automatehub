<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiMarketplaceController;
use App\Http\Controllers\ApiProxyController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Marketplace Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::prefix('api-marketplace')->name('api-marketplace.')->group(function () {
    Route::get('/', [ApiMarketplaceController::class, 'index'])->name('index');
    Route::get('/api/{slug}', [ApiMarketplaceController::class, 'show'])->name('show');
});

// Routes authentifiées
Route::middleware(['auth'])->prefix('api-marketplace')->name('api-marketplace.')->group(function () {
    Route::get('/dashboard', [ApiMarketplaceController::class, 'dashboard'])->name('dashboard');
    Route::post('/api/{slug}/subscribe', [ApiMarketplaceController::class, 'subscribe'])->name('subscribe');
    Route::get('/api/{slug}/payment/{subscription}', [ApiMarketplaceController::class, 'payment'])->name('payment');
    Route::post('/api/{slug}/buy-credits', [ApiMarketplaceController::class, 'buyCredits'])->name('buy-credits');
});

// Webhooks (sans authentification Laravel)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/stripe', [WebhookController::class, 'handleStripe'])->name('stripe');
    Route::post('/skool', [WebhookController::class, 'handleSkool'])->name('skool');
});

// API Proxy endpoints (authentification par clé API)
Route::prefix('api/proxy')->name('api.proxy.')->group(function () {
    Route::any('{api}/{endpoint}', [ApiProxyController::class, 'handle'])
        ->where('endpoint', '.*')
        ->name('handle');
});