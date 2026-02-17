<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WatchTrend\DashboardController;
use App\Http\Controllers\WatchTrend\WatchController;
use App\Http\Controllers\WatchTrend\InterestController;
use App\Http\Controllers\WatchTrend\SourceController;
use App\Http\Controllers\WatchTrend\SuggestionController;
use App\Http\Controllers\WatchTrend\PainPointController;
use App\Http\Controllers\WatchTrend\SettingsController;
use App\Http\Controllers\WatchTrend\OnboardingController;

/*
|--------------------------------------------------------------------------
| WatchTrend Routes
|--------------------------------------------------------------------------
| Routes pour l'application WatchTrend (veille intelligente multi-sources)
*/

Route::middleware(['auth'])->prefix('watchtrend')->name('watchtrend.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/suggestions', [DashboardController::class, 'suggestions'])->name('suggestions');

    // Watches CRUD
    Route::prefix('watches')->name('watches.')->group(function () {
        Route::get('/', [WatchController::class, 'index'])->name('index');
        Route::post('/', [WatchController::class, 'store'])->name('store');
        Route::get('/{watch}', [WatchController::class, 'show'])->name('show');
        Route::put('/{watch}', [WatchController::class, 'update'])->name('update');
        Route::delete('/{watch}', [WatchController::class, 'destroy'])->name('destroy');
        Route::post('/{watch}/pause', [WatchController::class, 'pause'])->name('pause');
        Route::post('/{watch}/resume', [WatchController::class, 'resume'])->name('resume');
        Route::post('/{watch}/archive', [WatchController::class, 'archive'])->name('archive');
        Route::post('/reorder', [WatchController::class, 'reorder'])->name('reorder');
    });

    // Interests
    Route::prefix('watches/{watch}/interests')->name('interests.')->group(function () {
        Route::get('/', [InterestController::class, 'index'])->name('index');
        Route::post('/', [InterestController::class, 'store'])->name('store');
        Route::put('/{interest}', [InterestController::class, 'update'])->name('update');
        Route::delete('/{interest}', [InterestController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [InterestController::class, 'reorder'])->name('reorder');
    });

    // Sources
    Route::prefix('sources')->name('sources.')->group(function () {
        Route::get('/', [SourceController::class, 'index'])->name('index');
        Route::post('/', [SourceController::class, 'store'])->name('store');
        Route::put('/{source}', [SourceController::class, 'update'])->name('update');
        Route::delete('/{source}', [SourceController::class, 'destroy'])->name('destroy');
        Route::post('/{source}/toggle', [SourceController::class, 'toggle'])->name('toggle');
        Route::post('/{source}/test', [SourceController::class, 'test'])->name('test');
        Route::post('/validate', [SourceController::class, 'validateSource'])->name('validate');
    });

    // Suggestions/Feedback
    Route::prefix('suggestions')->name('suggestions.')->group(function () {
        Route::post('/{analysis}/feedback', [SuggestionController::class, 'feedback'])->name('feedback');
        Route::post('/{item}/read', [SuggestionController::class, 'markRead'])->name('read');
        Route::post('/{item}/favorite', [SuggestionController::class, 'toggleFavorite'])->name('favorite');
    });

    // Pain Points
    Route::prefix('pain-points')->name('pain-points.')->group(function () {
        Route::get('/', [PainPointController::class, 'index'])->name('index');
        Route::post('/', [PainPointController::class, 'store'])->name('store');
        Route::put('/{painPoint}', [PainPointController::class, 'update'])->name('update');
        Route::delete('/{painPoint}', [PainPointController::class, 'destroy'])->name('destroy');
        Route::post('/{painPoint}/resolve', [PainPointController::class, 'resolve'])->name('resolve');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/preferences', [SettingsController::class, 'updatePreferences'])->name('update-preferences');
        Route::post('/api-key', [SettingsController::class, 'updateApiKey'])->name('update-api-key');
        Route::delete('/api-key', [SettingsController::class, 'deleteApiKey'])->name('delete-api-key');
    });

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/', [OnboardingController::class, 'index'])->name('index');
        Route::post('/interests', [OnboardingController::class, 'saveInterests'])->name('save-interests');
        Route::post('/sources', [OnboardingController::class, 'saveSources'])->name('save-sources');
        Route::get('/calibration', [OnboardingController::class, 'calibration'])->name('calibration');
        Route::post('/calibration/feedback', [OnboardingController::class, 'submitCalibrationFeedback'])->name('calibration-feedback');
        Route::post('/frequency', [OnboardingController::class, 'saveFrequency'])->name('save-frequency');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
    });
});
