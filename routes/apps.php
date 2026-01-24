<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppMarketplaceController;
use App\Http\Controllers\AppSettingsController;

/*
|--------------------------------------------------------------------------
| Apps Marketplace Routes (V2)
|--------------------------------------------------------------------------
*/

// Public marketplace
Route::prefix('apps')->name('apps.')->group(function () {
    Route::get('/', [AppMarketplaceController::class, 'index'])->name('index');
    Route::get('/{app:slug}', [AppMarketplaceController::class, 'show'])->name('show');
});

// Authenticated app management
Route::middleware(['auth'])->group(function () {
    // User's app subscriptions & management
    Route::prefix('my-apps')->name('my-apps.')->group(function () {
        Route::get('/', [AppMarketplaceController::class, 'myApps'])->name('index');
        Route::get('/{app:slug}', [AppMarketplaceController::class, 'myAppDashboard'])->name('dashboard');
    });

    // App settings & credentials
    Route::prefix('settings/apps')->name('settings.apps.')->group(function () {
        Route::get('/', [AppSettingsController::class, 'index'])->name('index');
        Route::get('/{app:slug}', [AppSettingsController::class, 'show'])->name('show');

        // Credentials management
        Route::post('/{app:slug}/credentials', [AppSettingsController::class, 'storeCredentials'])->name('credentials.store');
        Route::delete('/{app:slug}/credentials/{service}', [AppSettingsController::class, 'deleteCredentials'])->name('credentials.delete');
        Route::post('/{app:slug}/credentials/{service}/verify', [AppSettingsController::class, 'verifyCredentials'])->name('credentials.verify');
    });

    // Checkout & subscriptions
    Route::prefix('checkout/apps')->name('checkout.apps.')->group(function () {
        Route::post('/{app:slug}/{plan}', [AppMarketplaceController::class, 'checkout'])->name('create-session');
    });
});
